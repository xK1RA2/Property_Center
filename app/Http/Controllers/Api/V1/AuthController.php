<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = new User($data);
        $user->forceFill(['role_id' => User::ROLE_USER])->save();

        return $this->tokenResponse($user, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        return $this->tokenResponse($user);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('role'));
    }

    public function updateProfile(Request $request): UserResource
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'phone' => ['sometimes', 'nullable', 'string', 'max:45', Rule::unique('users', 'phone')->ignore($user->id)],
        ]);

        $user->update($data);

        return new UserResource($user->load('role'));
    }

    public function updatePhoto(Request $request): UserResource
    {
        $request->validate(['photo' => ['required', File::image()->max(2048)->dimensions(Rule::dimensions()->maxWidth(6000)->maxHeight(6000))]]);

        $user = $request->user();
        $user->update(['profile_image' => $request->file('photo')->store('profiles', 'public')]);

        return new UserResource($user->load('role'));
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages(['current_password' => ['Wrong password.']]);
        }

        $user->update(['password' => $data['password']]);

        // every other phone/browser that held a token must sign in again with the new password
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'Password updated']);
    }

    private function tokenResponse(User $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'user' => (new UserResource($user))->withEmail(),
            'token' => $user->createToken('api')->plainTextToken,
        ], $status);
    }
}
