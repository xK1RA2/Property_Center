<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\TraderRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = User::with('Rate')->findOrFail($request->user()->id);

        return view('home.profile', ['user' => $user, 'Avg' => $user->Rate->avg('Rate')]);
    }

    public function dealer(User $dealer)
    {
        // only dealers have a public profile; this must not be a way to read any user's details
        abort_unless($dealer->role_id === User::ROLE_DEALER, 404);

        return view('home.DealerProfile', [
            'user' => $dealer,
            'Properties' => Property::where('Dealer_id', $dealer->id)->get(),
        ]);
    }

    public function updateInformation(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'size:10', Rule::unique('users', 'phone')->ignore($user->id)],
        ]);

        $user->update($data);

        return redirect()->back()->with('Personal', 'Information Updated Successfully');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate(['image' => ['required', 'image', 'max:2048', 'dimensions:max_width=6000,max_height=6000']]);
        $user = $request->user();

        if ($user->profile_image && !str_starts_with($user->profile_image, 'http')) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->update(['profile_image' => $request->file('image')->store('user-profile', 'public')]);

        return redirect()->back()->with('success', 'Photo updated');
    }

    public function updatePassword(Request $request)
    {
        $request->validate(['newPassword' => ['required', Password::defaults()]]);
        $user = $request->user();

        if (!Hash::check((string) $request->input('password'), $user->password)) {
            return redirect()->back()->with('error', 'Wrong Password');
        }

        $user->update(['password' => $request->input('newPassword')]);

        // a changed password signs the phone app (API tokens) out everywhere
        $user->tokens()->delete();
        $request->session()->regenerate();

        return redirect()->back()->with('PasswordUpdated', 'Password Has Been Updated');
    }

    public function requestDealer(Request $request)
    {
        TraderRequest::firstOrCreate(['user_id' => $request->user()->id]);

        return redirect()->route('user.profile')->with('RequestSuccess', 'Request Has Been Submited');
    }
}
