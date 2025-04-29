  @props(['Property','isInWishList'=>false ])
  <!-- Property List Start -->
            @if($Property)
           
                          <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
      
                         
                                <div class="property-item rounded overflow-hidden">
                                    <div class="position-relative overflow-hidden">
                                        <a href="{{route('property.details',$Property->id)}}">  <img class="img-fluid img-thumbnail" src="https://images.crowdspring.com/blog/wp-content/uploads/2017/08/23163415/pexels-binyamin-mellish-106399.jpg" alt="Responsive image"></a>
                                        <div class="bg-primary rounded text-white position-absolute start-0 top-0 m-4 py-1 px-3">For {{$Property->PurchaseType}}</div>
                                        <div class="bg-white rounded-top text-primary position-absolute start-0 bottom-0 mx-4 pt-1 px-3">{{$Property->propertyType->name}}</div>
                                    </div>
                                    <div class="p-4 pb-0">
                                        <h5 class="text-primary mb-3">${{$Property->price}}</h5>
                                        <a class="d-block h5 mb-2" href="">{{$Property->PropertyType->name}}  For {{$Property->PurchaseType}}</a>
                                        <p><i class="fa fa-map-marker-alt text-primary me-2"></i>{{$Property->city->name}}  {{$Property->city->state->name}}</p>
                                    </div>
                                    <div class="d-flex border-top">
                                        <small class="flex-fill text-center border-end py-2"><i class="fa fa-ruler-combined text-primary me-2"></i>{{$Property->Features->Area}} Sqft</small>
                                        <small class="flex-fill text-center border-end py-2"><i class="fa fa-bed text-primary me-2"></i>{{$Property->Features->Bedrooms}} Bed</small>
                                        <small class="flex-fill text-center py-2 border-end py-2"><i class="fa fa-bath text-primary me-2"></i>{{$Property->Features->Bathrooms}} Bath</small>
                                        <small class="flex-fill text-center py-2 border-end py-2">
                                        <a class="btn-heart text-primary " 
        href="{{route('wishList.storeDestroy',$property)}}">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            style="width: 18px"
            @if($isInWishList )
                        hidden
                      @endif
                      >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
          </svg>
          <svg
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 24 24"
                      fill="currentColor"
                      style="width: 18px"
                      
                      @if(!$isInWishList )
                        hidden
                      @endif
                      >
                      <path
                        d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/>
          </svg>
        </a>
        </small>
                                    </div>
                                </div>
                            </div>
                            
                        @endif
          
             
        <!-- Property List End -->