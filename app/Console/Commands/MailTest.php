<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailTest extends Command
{
    protected $signature = 'app:mail-test {to : Address that should receive the test message}';

    protected $description = 'Send a test email with the configured mail settings and report the result';

    public function handle(): int
    {
        $to = (string) $this->argument('to');
        $mailer = config('mail.default');

        $this->line("Mailer: <info>{$mailer}</info>");

        if ($mailer === 'smtp') {
            $this->line('SMTP:   <info>' . config('mail.mailers.smtp.host') . ':' . config('mail.mailers.smtp.port') . '</info>'
                . ' (scheme: ' . (config('mail.mailers.smtp.scheme') ?: 'auto') . ')');
        }

        $this->line('From:   <info>' . config('mail.from.address') . '</info>');

        if (in_array($mailer, ['log', 'array'], true)) {
            $this->warn("MAIL_MAILER={$mailer}: messages are NOT delivered. Set MAIL_MAILER=smtp to send real email.");
        }

        try {
            Mail::raw(
                config('app.name') . " mail test\n\nIf you can read this, outgoing email works.\nSent: " . now()->toDateTimeString(),
                fn ($message) => $message->to($to)->subject(config('app.name') . ': mail test')
            );
        } catch (\Throwable $e) {
            $this->error('Sending failed: ' . $e->getMessage());
            $this->line('Check MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD and MAIL_SCHEME (smtp = STARTTLS on 587, smtps = TLS on 465).');

            return self::FAILURE;
        }

        $this->info("Accepted by the mail server for {$to}. Now check that inbox (and spam).");

        return self::SUCCESS;
    }
}
