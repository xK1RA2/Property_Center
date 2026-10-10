@props(['title' => 'Newsletter'])
<x-guest-layout :Tailwind="1">
    <div class="flex flex-col items-center justify-center p-8 bg-white min-h-screen">
        <div class="flex flex-col items-center rounded-3xl shadow-2xl shadow-gray-600/70 md:w-[500px] w-[300] p-8 text-center">
            <h1 class="text-3xl font-semibold text-gray-700 mb-4">{{ $title }}</h1>
            <p class="text-gray-600 mb-6">{{ $message }}</p>
            <a href="{{ route('home') }}"
               class="bg-green-500 text-white py-2 px-6 rounded-md hover:bg-green-600 transition">Back to the site</a>
        </div>
    </div>
</x-guest-layout>
