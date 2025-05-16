@props(['Tailwind'=>1])
<x-guest-layout :$Tailwind>
    <!-- container -->
    <div class="flex flex-col items-center justify-center  p-8 bg-white min-h-screen">
        <div class="flex flex-col items-center rounded-3xl shadow-2xl shadow-gray-600/70 md:w-[500px] w-[300] p-6">
            <h1 class="text-3xl font-semibold text-gray-700 flex items-center p-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500 fill-current mr-2"
                    viewBox="0 0 576 512">
                    <path
                        d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
                </svg>
                Welcome Home
            </h1>
            @error('email')
            <div class="text-red-500">Email or Password Doesn't Match</div>
          
            @enderror
            <form class="mt-4 w-full" action="{{route('login.store')}}" method="POST">
            @csrf    
            <!-- Email -->
                <div class="mb-4">
                    <label class="block text-gray-600 text-md font-bold">Email</label>
                    <div class="flex items-center border-b-2 border-gray-300 focus-within:border-green-500">
                        <span class="text-gray-400 p-2"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" required placeholder="name@example.com" name="email"
                            class="w-full p-2 focus:outline-none">
                    </div>
                </div>
                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-gray-600 text-md font-bold">Password</label>
                    <div class="flex items-center border-b-2 border-gray-300 focus-within:border-green-500">
                        <span class="text-gray-400 p-2"><i class="fa-solid fa-lock"></i></span>
                        <input id="password" type="password" required placeholder="••••••••"
                            class="w-full p-2 focus:outline-none" name="password">
                        <button type="button" onclick="togglePassword()"
                            class="text-gray-500 hover:text-gray-700 p-2">👁</button>
                    </div>
                </div>
             
             
                
          @if(session('error'))
            {{ session('error') }}
          @endif
         
            <div class="flex flex-col w-full md:flex-row md:space-x-2">
                    <a href="{{ route('login.oauth','google') }}"
                        class="f mt-2 w-full h-[50px] rounded-[10px] flex justify-center items-center font-medium gap-2 border border-[#ededef] bg-white cursor-pointer hover:bg-gray-50 transition duration-600">
                        <svg version="1.1" width="20" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512"
                            style="enable-background:new 0 0 512 512;" xml:space="preserve">
                            <path style="fill:#FBBB00;" d="M113.47,309.408L95.648,375.94l-65.139,1.378C11.042,341.211,0,299.9,0,256
                c0-42.451,10.324-82.483,28.624-117.732h0.014l57.992,10.632l25.404,57.644c-5.317,15.501-8.215,32.141-8.215,49.456
                C103.821,274.792,107.225,292.797,113.47,309.408z"></path>
                            <path style="fill:#518EF8;" d="M507.527,208.176C510.467,223.662,512,239.655,512,256c0,18.328-1.927,36.206-5.598,53.451
                c-12.462,58.683-45.025,109.925-90.134,146.187l-0.014-0.014l-73.044-3.727l-10.338-64.535
                c29.932-17.554,53.324-45.025,65.646-77.911h-136.89V208.176h138.887L507.527,208.176L507.527,208.176z">
                            </path>
                            <path style="fill:#28B446;" d="M416.253,455.624l0.014,0.014C372.396,490.901,316.666,512,256,512
                c-97.491,0-182.252-54.491-225.491-134.681l82.961-67.91c21.619,57.698,77.278,98.771,142.53,98.771
                c28.047,0,54.323-7.582,76.87-20.818L416.253,455.624z"></path>
                            <path style="fill:#F14336;" d="M419.404,58.936l-82.933,67.896c-23.335-14.586-50.919-23.012-80.471-23.012
                c-66.729,0-123.429,42.957-143.965,102.724l-83.397-68.276h-0.014C71.23,56.123,157.06,0,256,0
                C318.115,0,375.068,22.126,419.404,58.936z"></path>

                        </svg>

                        Google

                    </a>


                    <button
                        class="mt-2 w-full h-[50px] rounded-[10px] flex justify-center items-center font-medium gap-2 border border-[#ededef] bg-white cursor-pointer hover:bg-gray-50 transition duration-600">
                        <svg version="1.1" width="20" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50"
                            style="enable-background:new 0 0 50 50;" xml:space="preserve">
                            <rect width="50" height="50" rx="10" fill="#1877F2" />
                            <path
                                d="M30.5 25.5H26.75V37H21.5V25.5H19V21H21.5V18.25C21.5 16.375 22.25 13 27 13H31.5V17.5H28.5C27.75 17.5 26.75 17.875 26.75 19V21H31.5L30.5 25.5Z"
                                fill="white" />
                        </svg>
                        Facebook
                    </button>

                </div>
                   <!-- Button -->
                   <button type="submit"
                    class="mt-4 w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition">
                    Sign in
                </button>
                <div class="text-center mt-4">
                    <a href="#"
                        class="text-gray-500 hover:text-green-500 transition duration-300 hover:underline">Forgot
                        Password?</a>
                </div>
                  <!-- Sign up -->
            <div class="text-center mt-6 text-gray-600">
                Don't have an account?
                <a href="/signup" class="text-green-500 font-semibold hover:underline">Sign up</a>
            </div>
            <!-- Footer -->
            </form>
    </div>
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            password.type = password.type === 'password' ? 'text' : 'password';
        }
    </script>
</x-guest-layout >

