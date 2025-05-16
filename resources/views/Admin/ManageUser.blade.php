@props(['Tailwind'=>1,'active'=>'User','Users'])
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
                        <h3 class="text-3xl font-medium text-gray-700">Manage User</h3>

                        


                        <div class="container mt-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover text-center w-100">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th class="py-4 px-4">ID</th>
                                            <th class="py-4 px-4">Profile</th>
                                            <th class="py-4 px-4">Name</th>
                                            <th class="py-4 px-4">Email</th>
                                            <th class="py-4 px-4">Phone</th>
                                            <th class="py-4 px-4">Role</th>
                                            <th class="py-4 px-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($Users as $user)
                                        @if($user->role_id !== 3)
                                        <tr>
                                   
                                            <td class="px-4 py-4 text-center">{{$user->id}}</td>
                                            <td class="py-4 px-4 text-center"><img  src="{{
                    asset('storage/'.$user->profile_image) 
                    }}"  class="object-cover rounded w-16 h-16" alt="Profile"></td>
                                            <td class="px-4 py-4 text-center">{{$user->name}}</td>
                                            <td class="px-4 py-4 text-center">{{$user->email}}</td>
                                            <td class="px-4 py-4 text-center">{{$user->phone}}</td>
                                            <td class="px-4 py-4 text-center"><span class="badge fs-6 bg-green-500">{{ $user->role->name  }}</span></td>
        
                                            <td class="px-4 py-4 text-center ">
                                                <button data-ripple-light="true" type="button"
                                                    class="select-none rounded-lg bg-[#00B98E] py-3 px-8 text-center font-sans text-xs font-bold capitalize  text-white shadow-md shadow-green-500/20 transition-all hover:shadow-lg hover:shadow-green-500/40 focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50">
                                                    Edit
                                                </button>
                                             
                          <a 
                             href="{{route('DestroyUsers',$user)}}"
                          class="select-none rounded-lg bg-red-700 py-3 px-6 text-center font-sans text-xs font-bold capitalize text-white shadow-md shadow-red-500/20 transition-all hover:shadow-lg hover:shadow-red-500/40 focus:opacity-[0.85] active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50" >
                           
                            Delete
                          </a>
                        
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                      
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-guest-layout >
