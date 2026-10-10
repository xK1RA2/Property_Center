
@props(['title'=>'' , 'Tailwind'=>0])

<x-base-layout :$title :$Tailwind  >
    <x-layouts.header/>
        {{$slot}}
  
</x-base-layout>

