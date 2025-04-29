@props(['Tailwind'=>0,'title'=>"Profile",'Dealers','user','Properties'])

<x-app-layout :$Tailwind :$title>



    <div class="container shadow-lg mt-5 p-4 bg-white rounded">
        <div class="row">
            <!-- Left Section -->
            <div class="col-md-4  ">
                <div class="p-3 text-center border-0 ">
                    <h5 class="fw-bold text-muted mb-4">Dealer Account</h5>
                    <img id="profileImage" src="images (1).jpg" 
                         class="img-fluid rounded-circle mx-auto d-block mt-2 shadow-sm" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <input type="file" id="imageUpload" class="d-none" accept="image/*">
               
                </div>

             
              
            </div>

            <!-- Right Section -->
            <div class="col-md-8 ">
                <div class="card p-4 border-0  position-relative">
                    
                    <h4 class="fw-bold text-dark mb-4 border-bottom">Profile Information</h4>
                    <form id="profileForm" action="POST">
                        <div class="row mb-4">
                            <div class="col-md-6">
                               
                               
                                <div class="p-1  justify-content-between align-items-center">
                                   <label class="   text-dark fs-5 ">Username : </label>
                                    <input class="  border-0 border-bottom form-control  bg-white" id="username" value="{{$user->username}}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                            <div class="p-1  justify-content-between align-items-center">
                                   <label class="   text-dark fs-5 ">Name : </label>
                                    <input class="  border-0 border-bottom form-control  bg-white" id="name" value="{{$user->name}}" disabled>
                                </div>
                            </div>
                        </div>
            
                        <div class="mt-4">
                            <h4 class="fw-bold text-dark border-bottom">Contact Info</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                <div class="p-1  justify-content-between align-items-center">
                                   <label class="   text-dark fs-5 ">Email : </label>
                                    <input class="  border-0 border-bottom form-control  bg-white" id="email" value="{{$user->email}}" disabled>
                                </div>
                                </div>
                                <div class="col-md-6">
                                <div class="p-1  justify-content-between align-items-center">
                                   <label class="   text-dark fs-5 ">Phone : </label>
                                    <input class="  border-0 border-bottom form-control  bg-white" id="phone" value="{{$user->phone}}" disabled>
                                </div>
                                </div>
                            </div>
                        </div>
            
                     
                    
                
                    </form>

                  
                </div>
              

            </div>
        </div>
    </div>

    <div class="container shadow-lg mt-5 p-4 bg-white rounded">
    <div class="row">
   
    <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                          
        @foreach ($Properties as $Property )
        <x-property-item :$Property  :isInWishList="$Property->favouriteProperties->contains(\Illuminate\Support\Facades\Auth::user())"/>
        @endforeach
        </div>
        </div>
       </div>



    </div>
    </div>
    <!-- Bootstrap JS and JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
    </x-app-layout>

