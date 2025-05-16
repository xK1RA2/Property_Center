@props(['Tailwind'=>1,'active'=>'Property','Properties'])
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<x-guest-layout :$Tailwind>


<div class=" overflow-hidden flex flex-col  justify-center">
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

        <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-200">
            <div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
                class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>

            <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
                class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-[#00B98E] lg:translate-x-0 lg:static lg:inset-0">
               

                <x-Layouts.admin-header :$active />
            </div>
            <div class="flex flex-col flex-1 overflow-hidden">
                <header class="flex items-center justify-between px-6 py-4 bg-white border-b-4 border-[#00B98E]">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>

                        <div class="relative mx-4 lg:mx-0">



                        </div>
                    </div>

                    <div class="flex items-center">
                        <div x-data="{ notificationOpen: false }" class="relative">
                            <div x-show="notificationOpen" @click="notificationOpen = false"
                                class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                            <div x-show="notificationOpen"
                                class="absolute right-0 z-10 mt-2 overflow-hidden bg-white rounded-lg shadow-xl w-80"
                                style="width: 20rem; display: none;">
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Sara Salah</span> replied on the <span
                                            class="font-bold text-indigo-400" href="#">Upload Image</span> artical . 2m
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=634&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Slick Net</span> start following you . 45m
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1450297350677-623de575f31c?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Jane Doe</span> Like Your reply on <span
                                            class="font-bold text-indigo-400" href="#">Test with TDD</span> artical . 1h
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=398&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Abigail Bennett</span> start following you . 3h
                                    </p>
                                </a>
                            </div>
                        </div>

                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = ! dropdownOpen"
                                class="relative block w-12 h-12 overflow-hidden rounded-full shadow focus:outline-none">
                                <img class="object-cover w-full h-full"
                                    src="https://images.unsplash.com/photo-1528892952291-009c663ce843?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=296&amp;q=80"
                                    alt="Your avatar">
                            </button>

                            <div x-show="dropdownOpen" @click="dropdownOpen = false"
                                class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                            <div x-show="dropdownOpen"
                                class="absolute right-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl"
                                style="display: none;">
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#00B98E] hover:text-white">Profile</a>

                                <a href="{{route('logout')}}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#00B98E] hover:text-white">Logout</a>
                            </div>
                        </div>
                    </div>
                </header>
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                    <div class="container px-6 py-8 mx-auto">
                        <h3 class="text-3xl font-medium text-gray-700">Manage Property</h3>

                        <div class="container mt-4">
                        </div>


                        <div class="mt-6">

                        </div>


                     

                        <div class="container mt-4 text-center">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover text-center w-100">
                                    <thead class=" text-white">
                                        <tr>
                                            <th class="py-4 px-4">ID</th>
                                            <th class="py-4 px-4">Picture</th>
                                            <th class="py-4 px-4">Name</th>
                                            <th class="py-4 px-4">Location</th>
                                            <th class="py-4 px-4">Status</th>
                                            <th class="py-4 px-4">Price</th>
                                            <th class="py-4 px-4">Purchase Type</th>
                                            <th class="py-4 px-4">Date</th>
                                            <th class="py-4 px-4">Procedures</th>
                                    </thead>
                                    <tbody>
                                        @foreach($Properties as $Property)
                                        <tr>
                                            <td class="py-4 px-8">{{$Property->id}}</td>
                                            <td class=""><img src="{{$Property->PrimaryImage?->getUrl()}}" class="rounded img-fluid w-40 h-20" alt="Image"></td>
                                            <td class="py-4 px-8">{{$Property->PropertyType->name}}</td>
                                            <td class="py-4 px-8">{{$Property->city->name}} / {{$Property->city->state->name}}</td>
                                            <td class="py-4 px-8"><span class="badge @if($Property->status=='Available') bg-[#00B98E] @elseif($Property->status !=='Available') bg-red-700 @endif">{{$Property->status}}</span></td>
                                            <td class="py-4 px-8">${{$Property->price}}</td>
                                            <td class="py-4 px-8">{{$Property->PurchaseType}}</td>
                                            <td class="py-4 px-8">{{$Property->created_at}}</td>
                                            <td class="py-4 px-4 flex flex-col space-y-3 md:flex-row md:space-y-0 md:space-x-3"
                                                class="flex flex-col md:flex-row justify-center items-center gap-3 px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                                               


                                                <a href="{{route('EditProperty',$Property)}}" 
                          class="select-none rounded-lg bg-[#00B98E] py-3 px-8 text-center font-sans text-xs font-bold capitalize  text-white shadow-md shadow-green-500/20 transition-all hover:shadow-lg hover:shadow-green-500/40 focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 #  " >
                           
                            Edit
                          </a>
                          <a href="{{route('DestroyProperty',$Property)}}" 
                          class="select-none rounded-lg bg-red-700 py-3 px-6 text-center font-sans text-xs font-bold capitalize text-white shadow-md shadow-red-500/20 transition-all hover:shadow-lg hover:shadow-red-500/40 focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50" >
                           
                            Delete
                          </a>
                        

                                            </td>
                                        </tr>
                                        @endforeach
                                    

                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </div>
                </main>
            </div>
        </div>
    </div>

</x-guest-layout>
