@props(['Tailwind'=>1])
<x-guest-layout :$Tailwind>
    <div class="flex flex-col items-center justify-center p-8 bg-white min-h-screen">
        <div class="flex flex-col items-center rounded-3xl shadow-2xl shadow-gray-600/70 md:w-[500px] w-[300] p-6">
            <h1 class="text-3xl font-semibold text-gray-700 p-4">Choose a new password</h1>

            @if ($errors->any())
                <div class="text-red-500 mb-2">{{ $errors->first() }}</div>
            @endif

            <form class="mt-4 w-full" action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label class="block text-gray-600 text-md font-bold">Email</label>
                    <div class="flex items-center border-b-2 border-gray-300 focus-within:border-green-500">
                        <input type="email" required name="email" value="{{ old('email', $email) }}"
                            class="w-full p-2 focus:outline-none">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 text-md font-bold">New password</label>
                    <div class="flex items-center border-b-2 border-gray-300 focus-within:border-green-500">
                        <input type="password" required minlength="8" name="password" placeholder="••••••••"
                            class="w-full p-2 focus:outline-none">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 text-md font-bold">Confirm password</label>
                    <div class="flex items-center border-b-2 border-gray-300 focus-within:border-green-500">
                        <input type="password" required minlength="8" name="password_confirmation" placeholder="••••••••"
                            class="w-full p-2 focus:outline-none">
                    </div>
                </div>

                <button type="submit"
                    class="mt-4 w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition">
                    Update password
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
