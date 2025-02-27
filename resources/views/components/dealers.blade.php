@props(['dealer' ])

            
               
               
               <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="team-item rounded overflow-hidden">
                            <div class="position-relative">
                                <img class="img-fluid" src="{{$dealer->profile_image?->getUrl() ?: '/img/no-image.png'}}" alt="">
                                <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">

                                </div>
                            </div>

                            <div class="text-center p-4 mt-3">
                                <h5 class="fw-bold mb-0">{{$dealer->name}}</h5>
                                <small></small>
                            </div>
                        </div>
                    </div>
                    

         
        <!-- Team End -->

       

