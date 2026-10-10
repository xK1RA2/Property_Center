<?php

namespace App\Console\Commands;

use App\Mail\NewsletterIssue;
use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NewsletterSend extends Command
{
    protected $signature = 'newsletter:send
                            {subject : Email subject}
                            {--body= : Message text (or use --file)}
                            {--file= : Path to a plain-text file with the message}
                            {--dry-run : Show how many people would receive it, send nothing}';

    protected $description = 'Send a newsletter issue to everyone who confirmed their subscription';

    public function handle(): int
    {
        $body = $this->option('file')
            ? (is_file($this->option('file')) ? file_get_contents($this->option('file')) : null)
            : $this->option('body');

        if (!filled($body)) {
            $this->error('Provide the message with --body="..." or --file=path.txt');

            return self::FAILURE;
        }

        $total = NewsletterSubscriber::active()->count();

        if ($this->option('dry-run')) {
            $this->info("Would send to {$total} confirmed subscriber(s).");

            return self::SUCCESS;
        }

        if ($total === 0) {
            $this->warn('No confirmed subscribers yet.');

            return self::SUCCESS;
        }

        $subject = (string) $this->argument('subject');

        // each subscriber gets their own message so the unsubscribe link is personal;
        // the mailable is queued, so a worker (queue:work) delivers them
        NewsletterSubscriber::active()->orderBy('id')->each(
            fn (NewsletterSubscriber $s) => Mail::to($s->email)->queue(new NewsletterIssue($s, $subject, $body))
        );

        $this->info("Queued {$total} message(s). Make sure a queue worker is running (php artisan queue:work).");

        return self::SUCCESS;
    }
}
