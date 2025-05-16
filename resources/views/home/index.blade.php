@props(['title'=>'index'])

@auth
<script>
    var botmanWidget = {
        aboutText: 'Chat with us!',
        introMessage: "👋 Hi! I'm BotMan, how can I help you?",
        mainColor: '#23C39E', 
        bubbleBackground: '#23C39E',
    };
</script>

<script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
@endauth
<x-app-layout :$title>
<div class=" mx-3 bg-white p-0">
<div class="container-fluid header bg-white p-0">
            <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
                <div class="col-md-6 p-5 mt-lg-5">
                    <h1 class="display-5 animated fadeIn mb-4">Find A <span class="text-primary">Perfect Home</span> To Live With Your Family</h1>
    
                    <a href="" class="btn btn-primary py-3 px-5 me-3 animated fadeIn">Get Started</a>
                </div>

                <div class="col-md-6 animated fadeIn">
                    <div class="owl-carousel  header-carousel" >
                        <div class="owl-carousel-item" >
                            <img class="img-fluid" src="img/carousel-1.jpg" alt="">
                        </div>
                        <div class="owl-carousel-item">
                            <img class="img-fluid" src="img/carousel-2.jpg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        
          <!-- About Us -->
        <x-about-us />


         <!-- Property List Start -->
        <div class="container-xxl py-5">
            <div class="container">
                <div class="row g-0 gx-5 align-items-end">
                    <div class="col-lg-6">
                        <div class="text-start mx-auto mb-5 wow slideInLeft" data-wow-delay="0.1s">
                            <h1 class="mb-3">Property Listing</h1>
                        </div>
                    </div>
                    <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                        <ul class="nav nav-pills d-inline-flex justify-content-end mb-5">
                            <li class="nav-item me-2">
                                <a class="btn btn-outline-primary active" data-bs-toggle="pill" href="#tab-1">Featured</a>
                            </li>
               
                            
                        </ul>
                    </div>
         </div>
         
         <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                          
         @foreach($Properties as $Property)             
        <x-property-item :$Property
        :isInWishList="$Property->favouriteProperties->contains(\Illuminate\Support\Facades\Auth::user())" />
        @endforeach
         </div>
        </div>
       </div>
      
                        </div>
                        
                    </div>
                    <div class="col-12 text-center">
                                <a class="btn btn-primary py-3 px-5" href="{{ route('Purchase') }}">Browse More Property</a>
                            </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- Property List End -->


     <!-- Team Start -->
<div class="container-xxl py-5">
     <div class="container">
                <div class="text-center mx-auto mb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <h1 class="mb-3">Property Traders</h1>
                    <p>Top Rated Traders</p>
                </div>

                <div class="row g-4">
            
                @foreach($dealers as $dealer)
    <x-Dealers :$dealer />
                @endforeach
              </div>
              </div>
              </div>

 
              <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>

        <x-Layouts.footer/>
</x-app-layout>
