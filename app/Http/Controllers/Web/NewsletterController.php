<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NewsletterController extends Controller
{
    /** The footer form. Always answers the same, so the form can't be used to probe who is subscribed. */
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'newsletter_email' => ['required', 'email:rfc', 'max:255'],
        ], [
            'newsletter_email.required' => 'Please enter your email address.',
            'newsletter_email.email' => 'That does not look like a valid email address.',
        ]);

        $email = mb_strtolower(trim($data['newsletter_email']));
        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => $email]);

        // already subscribed and active: nothing to do, and no email to nag them with
        if (!$subscriber->isActive()) {
            // someone who left and comes back must confirm again
            if ($subscriber->unsubscribed_at) {
                $subscriber->forceFill(['unsubscribed_at' => null, 'confirmed_at' => null])->save();
            }

            try {
                Mail::to($subscriber->email)->send(new NewsletterConfirmation($subscriber));
            } catch (\Throwable $e) {
                // SMTP down / misconfigured: tell the visitor honestly rather than "check your inbox"
                Log::error('Newsletter confirmation email failed', ['email' => $email, 'error' => $e->getMessage()]);

                return redirect()->back()
                    ->withFragment('newsletter')
                    ->with('newsletter_error', 'We could not send the confirmation email right now. Please try again later.');
            }
        }

        return redirect()->back()
            ->withFragment('newsletter')
            ->with('newsletter_status', 'Almost done! Check your inbox and click the link to confirm.');
    }

    public function confirm(NewsletterSubscriber $subscriber)
    {
        $subscriber->forceFill(['confirmed_at' => $subscriber->confirmed_at ?? now(), 'unsubscribed_at' => null])->save();

        return view('newsletter.result', [
            'title' => 'You are subscribed',
            'message' => "Thanks for confirming! {$subscriber->email} will now receive our newsletter.",
        ]);
    }

    public function unsubscribe(NewsletterSubscriber $subscriber)
    {
        if (!$subscriber->unsubscribed_at) {
            $subscriber->forceFill(['unsubscribed_at' => now()])->save();
        }

        return view('newsletter.result', [
            'title' => 'You have been unsubscribed',
            'message' => "{$subscriber->email} will not receive any more newsletters.",
        ]);
    }
}
