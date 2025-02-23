
@props(['title'=>'' , 'Tailwind'=>0])

@if($Tailwind != 1)
    <x-base-layout :$title  >
    <x-Layouts.header/>
        {{$slot}}
    <x-Layouts.footer />
</x-base-layout>

@else
    <x-base-layout :$title :$Tailwind  >
    {{$slot}}  
    </x-base-layout>

@endif