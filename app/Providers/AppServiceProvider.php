<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Generate https:// links even if the proxy forgets X-Forwarded-Proto.
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        // One password policy for the website, the API, resets and the admin command.
        // In production passwords that appear in public breach lists are refused too.
        Password::defaults(fn () => Password::min(8)->letters()->numbers()
            ->when(app()->isProduction(), fn (Password $rule) => $rule->uncompromised()));

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // Sign-in: a few tries per account AND per address, so one attacker can neither hammer a
        // single account from many IPs nor spray many accounts from one IP.
        RateLimiter::for('login', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email'));

            return [
                Limit::perMinute(5)->by('login|' . $email . '|' . $request->ip()),
                Limit::perMinute(30)->by('login-ip|' . $request->ip()),
            ];
        });

        // Everything under /api: generous for normal use, a ceiling for scrapers and bugs.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)
            ->by($request->user()?->id ?: $request->ip()));

        // Things that create public content or notify other people.
        RateLimiter::for('writes', fn (Request $request) => Limit::perMinute(20)
            ->by($request->user()?->id ?: $request->ip()));
    }
}
