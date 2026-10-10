<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    private const PROVIDERS = ['google', 'facebook'];

    public function redirectToProvider($provider)
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        // Implement proper redirect to oauth provider
        return Socialite::driver($provider)->redirect();

    }

    public function handleCallback($provider)
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        try {
            // NOT stateless(): the OAuth "state" value must be checked, otherwise an attacker can
            // make a victim's browser finish the attacker's login (login CSRF).
            $social = Socialite::driver($provider)->user();

            $field = $provider . '_id';
            $email = mb_strtolower((string) $social->getEmail());

            if ($email === '') {
                return redirect(route('login'))->with('error', 'Your account has no email address we can use.');
            }

            $existing = User::where('email', $email)->first();

            if ($existing) {
                $alreadyLinked = $existing->{$field} !== null && hash_equals((string) $existing->{$field}, (string) $social->getId());

                // Linking a provider to a password account that already owns this email is only safe when
                // the provider proves the address is verified. Otherwise someone could register a provider
                // account with a victim's email and walk into the victim's account.
                if (!$alreadyLinked && !$this->emailIsVerifiedByProvider($provider, $social)) {
                    return redirect(route('login'))->with(
                        'error',
                        'An account with this email already exists. Sign in with your password first.'
                    );
                }

                if (!$alreadyLinked) {
                    $existing->forceFill([$field => $social->getId()])->save();
                }

                $user = $existing;
            } else {
                $user = new User(['name' => $social->getName() ?: $email, 'email' => $email]);
                $user->forceFill([
                    'role_id' => User::ROLE_USER,
                    $field => $social->getId(),
                    'email_verified_at' => $this->emailIsVerifiedByProvider($provider, $social) ? now() : null,
                ])->save();
            }

            Auth::login($user);
            request()->session()->regenerate();

            return $user->role_id === User::ROLE_ADMIN
                ? redirect()->intended(route('Dashboard'))
                : redirect()->intended(route('home'));
        } catch (\Throwable $e) {
            // log the detail, show nothing internal to the visitor
            Log::warning('Social login failed', ['provider' => $provider, 'error' => $e->getMessage()]);

            return redirect(route('login'))->with('error', 'We could not sign you in with ' . ucfirst($provider) . '. Please try again.');
        }
    }

    /** Google reports whether the address is verified; Facebook gives no such guarantee. */
    private function emailIsVerifiedByProvider(string $provider, $social): bool
    {
        if ($provider !== 'google') {
            return false;
        }

        $raw = (array) ($social->user ?? []);

        return filter_var($raw['email_verified'] ?? $raw['verified_email'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }
}