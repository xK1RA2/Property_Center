   <!-- Navbar Start -->
        <div class="container-fluid nav-bar bg-transparent">
            <nav class="navbar navbar-expand-lg bg-white navbar-light py-0 px-4">
                <a href="/index" class="navbar-brand d-flex align-items-center text-center">
                  
                    <h1 class="m-0 text-primary">Property Center</h1>
                </a>
                <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto">
                        <a href="/index" class="nav-item nav-link active">Home</a>

                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Property</a>
                            <div class="dropdown-menu rounded-0 m-0">

                                <a href="/View" class="dropdown-item">Purchase Property</a>
                                <a href="/myProperties" class="dropdown-item">My Properties</a>
                               
                                
                                
                            </div>
                        </div>
                        <a href="{{route('wishList.index')}}" class="nav-item nav-link">Wishlist</a>
                        <a href="contact.html" class="nav-item nav-link">DashBoard</a>
                        <a href="contact.html" class="nav-item nav-link">My Orders</a>
                    </div>
                    <a href="" class="btn btn-primary px-3 d-none d-lg-flex">Add Property</a>
                </div>
            </nav>
        </div>
        <!-- Navbar End -->
   