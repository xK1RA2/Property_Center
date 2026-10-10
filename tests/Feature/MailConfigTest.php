<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\MakesMarketplaceData;
use Tests\TestCase;

class MailConfigTest extends TestCase
{
    use RefreshDatabase, MakesMarketplaceData;

    public function test_mail_test_command_sends_a_message_and_reports_success(): void
    {
        config(['mail.default' => 'array']);

        $this->artisan('app:mail-test', ['to' => 'me@example.com'])
            ->expectsOutputToContain('Accepted by the mail server')
            ->assertSuccessful();

        $messages = Mail::mailer('array')->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $this->assertStringContainsString('me@example.com', $messages->first()->getEnvelope()->getRecipients()[0]->getAddress());
    }

    public function test_mail_test_command_surfaces_the_smtp_error(): void
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);

        $this->artisan('app:mail-test', ['to' => 'me@example.com'])
            ->expectsOutputToContain('Sending failed')
            ->assertFailed();
    }

    public function test_mail_test_command_warns_when_the_mailer_does_not_deliver(): void
    {
        config(['mail.default' => 'log']);

        $this->artisan('app:mail-test', ['to' => 'me@example.com'])
            ->expectsOutputToContain('NOT delivered')
            ->assertSuccessful();
    }

    public function test_smtp_settings_are_read_from_the_environment_keys_documented_in_env_example(): void
    {
        $smtp = config('mail.mailers.smtp');

        foreach (['scheme', 'host', 'port', 'username', 'password'] as $key) {
            $this->assertArrayHasKey($key, $smtp);
        }
        $this->assertNotEmpty(config('mail.from.address'));
    }

    public function test_password_reset_mail_uses_the_mail_system(): void
    {
        Notification::fake();
        $this->seedLookups();
        $user = $this->makeUser(attrs: ['email' => 'reset@example.com']);

        $this->post('/ForgotUpdate', ['email' => 'reset@example.com']);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
