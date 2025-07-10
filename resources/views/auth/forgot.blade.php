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
                Forgot Password
            </h1>
            @error('email')
            <div class="text-red-500">Email or Password Doesn't Match</div>
          
            @enderror
            <form class="mt-4 w-full" action="{{route('ForgotUpdate')}}" method="POST">
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
                    <label class="block text-gray-600 text-md font-bold">New Password</label>
                    <div class="flex items-center border-b-2 border-gray-300 focus-within:border-green-500">
                        <span class="text-gray-400 p-2"><i class="fa-solid fa-lock"></i></span>
                        <input id="password" type="password" required placeholder="••••••••"
                            class="w-full p-2 focus:outline-none" name="password">
                        <button type="button" onclick="togglePassword()"
                            class="text-gray-500 hover:text-gray-700 p-2">👁</button>
                    </div>
                </div>
              <div class="mb-4">
                    <label class="block text-gray-600 text-md font-bold">Confirm Password</label>
                    <div class="flex items-center border-b-2 border-gray-300 focus-within:border-green-500">
                        <span class="text-gray-400 p-2"><i class="fa-solid fa-lock"></i></span>
                        <input id="password" type="password" required placeholder="••••••••"
                            class="w-full p-2 focus:outline-none" name="Confirmpassword">
                        <button type="button" onclick="togglePassword()"
                            class="text-gray-500 hover:text-gray-700 p-2">👁</button>
                    </div>
                </div>
             
                
          @if(session('error'))
            {{ session('error') }}
          @endif
         
         
                   <!-- Button -->
                   <button type="submit"
                    class="mt-4 w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition">
                    Submit
                </button>
             
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

