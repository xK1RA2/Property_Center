<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * Standard emailed-link reset. (The old "forgot" form changed ANY user's
 * password given only their email address.)
 */
class PasswordResetController extends Controller
{
    public function forgot()
    {
        return view('auth.forgot');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink($request->only('email'));

        // same answer whether or not the account exists, so emails can't be enumerated
        return back()->with('status', 'If that email has an account, a reset link is on its way.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            // revoke API tokens too: a reset means the old credentials may be compromised
            $user->tokens()->delete();
            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)])->onlyInput('email');
        }

        return redirect()->route('login')->with('success', 'Password updated, you can sign in now.');
    }
}
