@props(['title'=>'Details'])

<x-app-layout :$title>
<div class="container my-5">
        <div class="row g-4 " >
            <!-- السلايدر الخاص في الصور -->
            <div class="col-md-6" >
                <div id="propertyCarousel"class="carousel slide shadow"data-bs-ride="carousel">
                    <div class="carousel-inner">
                    <div class="carousel-item active">
                        
                        <img src="{{$Property->PrimaryImage->getUrl()}}"  alt="" />
                        </div>
                    @foreach ($Property->PropertyImages as $image)

                    <div class="carousel-item ">
                        
                  <img src="{{$image->getUrl()}}" alt="" />
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
                            <a href="tel:+962798677535"class="btn btn-primary">
                                <i class="bi bi-telephone-fill me-2"></i>Call: {{$Property->Dealer->phone}}
                            </a>
                            <a href="mailto:info@property.com"class="btn btn-primary">
                                <i class="bi bi-envelope-fill me-2"></i>Email: {{$Property->Dealer->email}}
                            </a>
                               <!--  زر لحجز موعد مراجعة عقار-->
                               <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookingModal">
                                <i class="bi bi-calendar-check-fill me-2"></i>Book an Appointment
                            </button>
       
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Book an Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phoneNumber" placeholder="Enter your phone number" required>
                        </div>
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
                            <div class="d-flex align-items-center justify-content-start gap-3 mb-3 p-3 bg-light rounded">
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
                                <button type="submit"class="btn btn-primary">Submit Rating</button>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
<!-- التعليقات -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow border-0">
            <div class="card-body">
                <h5 class="card-title text-dark fw-bold mb-4">Comments</h5>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-light p-3 rounded mb-2 d-flex align-items-center gap-3 border">
                        <img src="./images/person1.png" class="rounded-circle" alt="Ahmed's avatar" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="d-flex flex-column">
                            <strong class="text-dark">Ahmed</strong>
                            <span class="text-muted">Great property!</span>
                        </div>
                    </li>
                    <li class="list-group-item bg-light p-3 rounded mb-2 d-flex align-items-center gap-3 border">
                        <img src="./images/person2.png" class="rounded-circle" alt="Sarah's avatar" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="d-flex flex-column">
                            <strong class="text-dark">Sarah</strong>
                            <span class="text-muted">Excellent location</span>
                        </div>
                    </li>
                    <li class="list-group-item bg-light p-3 rounded mb-2 d-flex align-items-center gap-3 border">
                        <img src="./images/person1.png" class="rounded-circle" alt="Mohammed's avatar" style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="d-flex flex-column">
                            <strong class="text-dark">Mohammed</strong>
                            <span class="text-muted">Reasonable price</span>
                        </div>
                    </li>
                </ul>
                <form>
                    <div class="mb-3">
                        <textarea class="form-control" rows="3" placeholder="Add your comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Comment</button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
