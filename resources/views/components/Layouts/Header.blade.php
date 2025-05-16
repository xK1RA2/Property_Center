
 <!-- Navbar Start -->
        <div class="container-fluid nav-bar bg-transparent  " id="navbar">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-0 px-4 " >
                <a href="/index" class="navbar-brand d-flex align-items-center text-center">
                  
                    <h2 class="m-0 text-primary">Property Center</h2>
                </a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto">
                      
                        @auth()
                        <a href="/index" class="nav-item nav-link active">Home</a>
                        @if(request()->user()->role_id==2)
                        <div class="nav-item dropdown">
                    
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Property</a>
                       
                        <div class="dropdown-menu rounded-0 m-0">
                          
                               
                                <a href="/myProperties" class="dropdown-item">My Properties</a>
                                <a href="{{route('property')}}" class="dropdown-item">Add Property</a>
                                <a href="/View" class="dropdown-item ">Purchase Property</a>
                                
                            </div>
                            </div>
                            @else
                            <a href="/View" class="nav-item nav-link ">Purchase Property</a>
                            @endif
                            @endauth
                     
                        @auth()
                        @if(request()->user()->role_id==1)
                        <a href="{{route('wishList.index')}}" class="nav-item nav-link">Wishlist</a>
                        @endif
                        @if(request()->user()->role_id==2)
                        
                        <a href="{{route('index.dashboard')}}" class="nav-item nav-link">DashBoard</a>
                        
                        @endif
                        <a href="{{route('maps')}}" class="nav-item nav-link">Map</a>
                        <a href="{{route('Dealers')}}" class="nav-item nav-link">Dealers</a>
                       
                        @endauth
                       
                        @auth()
                        <div class="nav-item  dropstart ">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"><i class="fa-regular fa-user"></i></a>
                            <div class="dropdown-menu rounded-0 m-0">
                           
                            <a href="{{route('user.profile')}}" class="dropdown-item">Profile</a>
                            
                            <a href="{{route('orders')}}" class="dropdown-item">My Orders</a>
                              
                                <a href="{{route('logout')}}" class="dropdown-item">Logout</a>
                                
                               
                            </div>
                            </div>
                            @endauth
                            
                            @guest
                     
                                <a href="{{route('login')}}"class="nav-item nav-link bg-primary  rounded px-4  text-white  hover-bg-dark ">Login</a>
                                <a href="{{route('signup')}}" class="nav-item nav-link ">Signup</a>
                               
                                @endguest
                       
                    </div>
                   
                </div>
            </nav>
        </div>
        <!-- Navbar End -->
   