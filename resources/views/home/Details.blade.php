@props(['title'=>'Details','Comments'])

<div class="modal fade " id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true" style="z-1050 ; margin-top:40px">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Book an Appointment</h5>
                  
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm" action="{{ route('Booking',$Property) }}">
                     
                        <div class="mb-3">
                            <label for="appointmentDate" class="form-label">Preferred Date</label>
                            <input type="date" class="form-control" id="appointmentDate" name="date" required>
                        </div>
                       
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" rows="3" name="description" placeholder="Any additional information..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Booking</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<x-app-layout :$title>
<div class="container my-5">
        <div class="row g-4 " >
            <!-- السلايدر الخاص في الصور -->
            <div class="col-md-6" >
                <div id="propertyCarousel"class="carousel slide shadow"data-bs-ride="carousel">
                    <div class="carousel-inner">
                    <div class="carousel-item active">
<!--                         
                        <img src="{{$Property->PrimaryImage->getUrl()}}"  alt="" /> -->
                        <img src="https://images.crowdspring.com/blog/wp-content/uploads/2017/08/23163415/pexels-binyamin-mellish-106399.jpg"  alt="" />
                        </div>
                    @foreach ($Property->PropertyImages as $image)

                    <div class="carousel-item ">
                        
                  <img src="{{$image->getUrl()}}" alt=""  height="100%" width="100%">
                  </div>
                  @endforeach
                        
                    </div>

                    <button class="carousel-control-prev"type="button"data-bs-target="#propertyCarousel"data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next"type="button"data-bs-target="#propertyCarousel"data-bs-slide="next">
                        <span class="carousel-control-next-icon"aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
      
            <!-- المعلومات الي على يمين الشاشة -->
            <div class="col-md-6">
                <div class="card h-100 shadow border-0">
                    <div class="card-body">
                        <h2 class="card-title text-dark fw-bold"> {{$Property->PropertyType->name}} in {{$Property->city->state->name}}</h2>
                        <p class="card-text text-muted">An {{$Property->PropertyType->name}} with {{$Property->Features->Bedrooms}} bedrooms and {{$Property->Features->Bathrooms}} bathrooms, {{$Property->Features->Area}} square meters, prime location near services.</p>
                        <h4 class="text-primary fw-bold">Price: {{$Property->price}} $</h4>
                        <ul class="list-group list-group-flush mb-4">
                        
                        @if($Property->Features->Parking) <li class="list-group-item bg-light"> Parking  </li>@endif
                            @if($Property->Features->Heating)  <li class="list-group-item bg-light"> Heating  </li>@endif
                            @if($Property->Features->Air_Conditioner) <li class="list-group-item bg-light"> Air Conditioning  </li>@endif
                           
                        </ul>
                        <!-- للتواصل -->
                        <h5 class="text-dark fw-bold mt-4">Contact Owner:</h5>
                        <div class="d-flex flex-column gap-2">
                            <a href="tel:+962798677535"class="">
                                <i class="bi bi-telephone-fill me-2"></i>Call: {{$Property->Dealer->phone}}
                            </a>
                            <a href="mailto:info@property.com"class="">
                                <i class="bi bi-envelope-fill me-2"></i>Email: {{$Property->Dealer->email}}
                            </a>
                               <!--  زر لحجز موعد مراجعة عقار-->
                               @auth()
                               @if(session('BookingSuccess'))
                            <div class="alert alert-success">
                        {{ session('BookingSuccess') }}
                        </div>
                             @endif
                               @if(session('BookingUpdated'))
                            <div class="alert alert-info">
                        {{ session('BookingUpdated') }}
                        </div>
                             @endif
                               <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                <i class="bi bi-calendar-check-fill me-2"></i>Book an Appointment
                            </button>
                            @if(session('Past'))
                    <div class="alert alert-danger">
                        {{ session('Past') }}
                        </div>
                    @endif
                    @if(session('CheckoutSuccess'))
                    <div class="alert alert-success">
                        {{ session('CheckoutSuccess') }}
                        </div>
                    @endif
                            @if(session('ExpDate'))
                    <div class="alert alert-danger">
                        {{ session('ExpDate') }}
                        </div>
                    @endif
                            @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                            <button type="button" class="btn btn-primary  " data-bs-toggle="modal" data-bs-target="#checkoutModal">
         Checkout 
    </button>
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Complete Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- الاسم -->
                 <form action="{{ route('checkout',$Property) }}" >
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter your full name" required>
                    </div>
                    <!-- الايميل -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
                    </div>
                    <!--  حقل لنوع الدفع بس للباك ببين عامله زي ماطلبت-->
                    <input type="hidden" id="paymentMethod" name="paymentMethod" value="">
                    <div class="mb-3">
                        <label for="cardNumber" class="form-label">Card Number</label>
                        <div class="card-number-container">
                            <input type="text" class="form-control" name="CardNumber" id="cardNumber" placeholder="xxxx xxxx xxxx xxxx" maxlength="19" required>
                            <i class="fa-solid fa-credit-card card-icon" id="cardIcon"></i>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="expiry" class="form-label">Expiry Date</label>
                            <input type="date" name="expDate" class="form-control" id="expiry" placeholder="MM/YY" required>
                            <!-- رسالة الخطا لتاريخ البطاقة -->
                            <div id="expiry-error" class="error-message">Please enter a valid expiry date (MM/YY) that is not expired.</div>
                        </div>
                        <div class="col">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" class="form-control" name="cvv" id="cvv" placeholder="123" maxlength="3" required>
                            <!-- رسالة خطا اذا غير 3 ارقام -->
                            <div id="cvv-error" class="error-message">CVV must be exactly 3 digits.</div>
                        </div>
                    </div>
                    @if($Property->PurchaseType == "Rent")
                    <div class="row">
                    <div class="col">
                            <label for="expiry" class="form-label">Date From</label>
                            <input type="date" name="From" class="form-control" id="expiry" placeholder="MM/YY" required>
                            <!-- رسالة الخطا لتاريخ البطاقة -->
                            <div id="expiry-error" class="error-message">Please enter a valid expiry date (MM/YY) that is not expired.</div>
                        </div>  <div class="col">
                            <label for="expiry" class="form-label">Date To</label>
                            <input type="date" name="To" class="form-control" id="expiry" placeholder="MM/YY" required>
                            <!-- رسالة الخطا لتاريخ البطاقة -->
                            <div id="expiry-error" class="error-message">Please enter a valid expiry date (MM/YY) that is not expired.</div>
                        </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter your address" required></textarea>
                    </div>
                </div>
             
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary" >Close</button>
                    <button type="submit" class="btn btn-primary" >Complete Payment</button>
                </div>
            </div>
        </div>
        </form>
    </div>
                            @endauth
                            @guest()
                            <a href="{{ route('login') }}" class="text-white">
                            <button  class="btn btn-primary">
                                <i class="bi bi-calendar-check-fill me-2"></i>Book an Appointment
                                </a>
                                @endguest
                       


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            @auth
                <div class="modal-header">
             
                    <h5 class="modal-title" id="bookingModalLabel">Book an Appointment</h5>
                    <button type="button" class="btn-close" ></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm" method="post" action="{{ route('Book',['id'=>$Property->id]) }}">
                        
                        <div class="mb-3">
                            <label for="appointmentDate" class="form-label">Preferred Date</label>
                            <input type="date" class="form-control" id="appointmentDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="appointmentTime" class="form-label">Preferred Time</label>
                            <input type="time" class="form-control" id="appointmentTime" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" rows="3" placeholder="Any additional information..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit Booking</button>
                        
                    </form>
                </div>
                @endauth
                
            </div>
        </div>
    </div>
        <!-- التقيمات -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5 class="card-title text-dark fw-bold">Property Rating</h5>
                        <div class="text-warning mb-4 fs-4">
                            
                          
                            ★★★★☆ <span class="text-muted fs-6">({{$Property->PropertyRate->avg('Rate')}} out of 5 - Based on {{$Property->PropertyRate->count('Rate')}} reviews)</span>
                        </div>
                        <!-- كيف تحط تقيم -->
                        <form id="ratingForm" action="{{route('rate.store', $Property)}}" method="post" >
                            @csrf
                            <h6 class="fw-bold text-primary mb-3">Choose Your Rating:</h6>
                            @if(session('RateSubmit'))
                            <div class="alert alert-success text-dark">{{ session('RateSubmit') }}</div>
                            @elseif(session('update'))
                            <div class="alert alert-info text-dark">{{ session('update') }}</div>
                            
                            @endif
                            <div class="d-flex align-items-center justify-content-start gap-3 mb-3 p-3 rounded">
                                <div class="btn-group"role="group"aria-label="Star Rating">
                                    <input type="radio"class="btn-check"name="rating"id="star1"value="1"autocomplete="off">
                                    <label class="btn btn-outline-warning fs-5 px-1 py-0"for="star1">★</label>
                                    <input type="radio"class="btn-check"name="rating"id="star2"value="2"autocomplete="off">
                                    <label class="btn btn-outline-warning fs-5 px-1 py-0"for="star2">★</label>
                                    <input type="radio"class="btn-check"name="rating"id="star3"value="3"autocomplete="off">
                                    <label class="btn btn-outline-warning fs-5 px-1 py-0"for="star3">★</label>
                                    <input type="radio"class="btn-check"name="rating"id="star4"value="4"autocomplete="off">
                                    <label class="btn btn-outline-warning fs-5 px-1 py-0" for="star4">★</label>
                                    <input type="radio"class="btn-check"name="rating"id="star5"value="5"autocomplete="off">
                                    <label class="btn btn-outline-warning fs-5 px-1 py-0"for="star5">★</label>
                                </div>
                                @guest()
                        <a href="{{route('login')}}" class="btn btn-primary">Submit Rating</a>
                                @endguest
                                @auth
                                <button type="submit"class="btn btn-primary">Submit Rating</button>
                                @endauth()
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
        <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold mb-4">Comments</h5>
                @if(session('Delete'))
                <div class="alert alert-danger">
                    {{ session('Delete') }}
                    </div>
                @endif

                @if(session('success'))
                <div class="alert alert-success text-dark">
{{ session('success') }}
                </div>
                @endif
                @foreach($Comments as $Comment)
<x-PropertyComments :$Comment />
    @endforeach
    <form action="{{ route('Comments',$Property) }}">
                 
                    <div class="mb-3">
                        <textarea class="form-control" rows="3" placeholder="Add your comment..." required name="Description"></textarea>
                        <label for="" class="fw-bold fs-5 me-2">Ananymous Comment</label>
                        <input type="checkbox" name="Ananymous" value="Ananymous" class="mt-5" >
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Comment</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="./js/bootstrap.bundle.min.js"></script>
   
    <script>
      

       
        
    </script>
</x-app-layout>
