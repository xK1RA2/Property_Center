
@props(['title'=>''])

<x-base-layout :$title >
    <x-Layouts.header/>
        {{$slot}}
    <x-Layouts.footer />
</x-base-layout>