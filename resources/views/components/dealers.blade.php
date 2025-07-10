@props(['dealer' ])

            
               
               
               <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="team-item rounded overflow-hidden">
                        <a href="{{ route('Dealer_Profile',$dealer) }}">
                            <div class="position-relative">
                               
                                <img class="img-fluid" src="{{
                            asset('storage/'.$dealer->profile_image) 
                            }}" alt="" style="height: 200px;width:300px;">
                                <div class="position-absolute start-50 top-100 translate-middle d-flex align-items-center">

                                </div>
                            </div>
                                
                            <div class="text-center p-4 mt-3">
                                <hr>
                                <h5 class="fw-bold mb-3">{{$dealer->name}}</h5>
                                <small></small>
                                 
                                 <?php $i=1 ;
                    
                    $Avg = $dealer->Rate->avg('Rate'); ?>
                    @for($i; $i<=$Avg ; $i++)
                       <span class="text-warning"> <i class="fa-solid fa-star"></i> </span>
                    @endfor
                    
                    @if($i - $Avg < 1 )
                    <i class="fa-solid fa-star-half-stroke"></i>
                    @endif
                            </div>
                            </a>
                        </div>
                    </div>
                    

         
        <!-- Team End -->

       

