<?php

namespace Tests\Feature;

use App\Mail\NewsletterConfirmation;
use App\Mail\NewsletterIssue;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedLookups();
    }

    /** Subscribers are created through forceFill: confirmed_at must never be mass-assignable. */
    private function subscriber(array $attrs): NewsletterSubscriber
    {
        $subscriber = new NewsletterSubscriber();
        $subscriber->forceFill($attrs)->save();

        return $subscriber;
    }

    public function test_footer_has_a_working_subscribe_form(): void
    {
        $this->get('/index')
            ->assertOk()
            ->assertSee('name="newsletter_email"', false)
            ->assertSee(route('newsletter.subscribe'), false);
    }

    public function test_subscribing_stores_the_address_unconfirmed_and_sends_a_confirmation(): void
    {
        Mail::fake();

        $this->from('/index')
            ->post('/newsletter', ['newsletter_email' => '  New.Person@Example.COM '])
            ->assertRedirect('/index#newsletter')
            ->assertSessionHas('newsletter_status');

        $subscriber = NewsletterSubscriber::firstOrFail();
        $this->assertSame('new.person@example.com', $subscriber->email);
        $this->assertNull($subscriber->confirmed_at);

        Mail::assertSent(NewsletterConfirmation::class, fn ($m) => $m->hasTo('new.person@example.com'));
    }

    public function test_the_confirmation_email_contains_a_working_signed_link(): void
    {
        Mail::fake();
        $this->post('/newsletter', ['newsletter_email' => 'a@example.com']);

        $subscriber = NewsletterSubscriber::firstOrFail();

        Mail::assertSent(NewsletterConfirmation::class, function (NewsletterConfirmation $mail) {
            $html = $mail->render();

            return str_contains($html, 'newsletter/confirm/') && str_contains($html, 'signature=');
        });

        $this->get($subscriber->confirmUrl())
            ->assertOk()
            ->assertSee('You are subscribed');

        $this->assertTrue($subscriber->fresh()->isActive());
    }

    public function test_confirm_and_unsubscribe_links_cannot_be_forged(): void
    {
        $subscriber = $this->subscriber(['email' => 'victim@example.com']);

        // no signature
        $this->get("/newsletter/confirm/{$subscriber->id}")->assertForbidden();
        $this->get("/newsletter/unsubscribe/{$subscriber->id}")->assertForbidden();

        // signature for a different subscriber
        $other = $this->subscriber(['email' => 'other@example.com']);
        $forged = str_replace("/confirm/{$other->id}", "/confirm/{$subscriber->id}", $other->confirmUrl());
        $this->get($forged)->assertForbidden();

        $this->assertNull($subscriber->fresh()->confirmed_at);
    }

    public function test_unsubscribe_link_removes_the_person_from_the_list(): void
    {
        $subscriber = $this->subscriber(['email' => 'leaver@example.com', 'confirmed_at' => now()]);
        $this->assertTrue($subscriber->isActive());

        $this->get($subscriber->unsubscribeUrl())->assertOk()->assertSee('You have been unsubscribed');

        $this->assertFalse($subscriber->fresh()->isActive());
        $this->assertSame(0, NewsletterSubscriber::active()->count());
    }

    public function test_resubscribing_after_leaving_needs_a_new_confirmation(): void
    {
        Mail::fake();
        $subscriber = $this->subscriber([
            'email' => 'back@example.com', 'confirmed_at' => now()->subWeek(), 'unsubscribed_at' => now()->subDay(),
        ]);

        $this->post('/newsletter', ['newsletter_email' => 'back@example.com'])->assertSessionHas('newsletter_status');

        $fresh = $subscriber->fresh();
        $this->assertNull($fresh->unsubscribed_at);
        $this->assertNull($fresh->confirmed_at);
        Mail::assertSent(NewsletterConfirmation::class, 1);
    }

    public function test_an_already_active_subscriber_gets_no_second_email_and_no_duplicate(): void
    {
        Mail::fake();
        $this->subscriber(['email' => 'here@example.com', 'confirmed_at' => now()]);

        $this->post('/newsletter', ['newsletter_email' => 'HERE@example.com'])->assertSessionHas('newsletter_status');

        Mail::assertNothingSent();
        $this->assertSame(1, NewsletterSubscriber::count());
    }

    public function test_an_unconfirmed_address_can_ask_for_the_email_again_without_duplicates(): void
    {
        Mail::fake();

        $this->post('/newsletter', ['newsletter_email' => 'twice@example.com']);
        $this->post('/newsletter', ['newsletter_email' => 'twice@example.com']);

        $this->assertSame(1, NewsletterSubscriber::count());
        Mail::assertSent(NewsletterConfirmation::class, 2);
    }

    public function test_invalid_input_is_rejected(): void
    {
        Mail::fake();

        $this->post('/newsletter', ['newsletter_email' => 'not-an-email'])->assertSessionHasErrors('newsletter_email');
        $this->post('/newsletter', [])->assertSessionHasErrors('newsletter_email');

        $this->assertSame(0, NewsletterSubscriber::count());
        Mail::assertNothingSent();
    }

    public function test_a_mail_server_failure_is_reported_honestly(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('Connection refused'));

        $this->post('/newsletter', ['newsletter_email' => 'a@example.com'])
            ->assertSessionHas('newsletter_error')
            ->assertSessionMissing('newsletter_status');
    }

    public function test_subscribing_is_rate_limited(): void
    {
        Mail::fake();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/newsletter', ['newsletter_email' => "p{$i}@example.com"])->assertRedirect();
        }

        $this->post('/newsletter', ['newsletter_email' => 'one-too-many@example.com'])->assertStatus(429);
    }

    // ---------- sending issues ----------

    public function test_issue_goes_only_to_confirmed_active_subscribers(): void
    {
        Mail::fake();
        $this->subscriber(['email' => 'yes1@example.com', 'confirmed_at' => now()]);
        $this->subscriber(['email' => 'yes2@example.com', 'confirmed_at' => now()]);
        $this->subscriber(['email' => 'unconfirmed@example.com']);
        $this->subscriber(['email' => 'left@example.com', 'confirmed_at' => now(), 'unsubscribed_at' => now()]);

        $this->artisan('newsletter:send', ['subject' => 'June offers', '--body' => "Hello\nBig news"])
            ->expectsOutputToContain('Queued 2 message(s)')
            ->assertSuccessful();

        Mail::assertQueued(NewsletterIssue::class, 2);
        Mail::assertQueued(NewsletterIssue::class, fn ($m) => $m->hasTo('yes1@example.com') && $m->subjectLine === 'June offers');
        Mail::assertNotQueued(NewsletterIssue::class, fn ($m) => $m->hasTo('unconfirmed@example.com'));
        Mail::assertNotQueued(NewsletterIssue::class, fn ($m) => $m->hasTo('left@example.com'));
    }

    public function test_each_issue_carries_a_personal_unsubscribe_link_and_escapes_the_body(): void
    {
        $subscriber = $this->subscriber(['email' => 'reader@example.com', 'confirmed_at' => now()]);

        $mail = new NewsletterIssue($subscriber, 'Subject', "Line one\n<script>alert(1)</script>");
        $html = $mail->render();

        $this->assertStringContainsString('newsletter/unsubscribe/' . $subscriber->id, $html);
        $this->assertStringContainsString('Line one<br', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertArrayHasKey('List-Unsubscribe', $mail->headers()->text);
    }

    public function test_dry_run_sends_nothing(): void
    {
        Mail::fake();
        $this->subscriber(['email' => 'yes@example.com', 'confirmed_at' => now()]);

        $this->artisan('newsletter:send', ['subject' => 'S', '--body' => 'B', '--dry-run' => true])
            ->expectsOutputToContain('Would send to 1')
            ->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_send_requires_a_message(): void
    {
        $this->artisan('newsletter:send', ['subject' => 'S'])->assertFailed();
        $this->artisan('newsletter:send', ['subject' => 'S', '--file' => 'does-not-exist.txt'])->assertFailed();
    }
}
