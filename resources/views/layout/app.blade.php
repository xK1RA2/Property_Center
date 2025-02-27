
@props(['title'=>'' , 'Tailwind'=>0])


<x-base-layout :$title :$Tailwind  >
    <x-Layouts.header/>
        {{$slot}}
  
</x-base-layout>

