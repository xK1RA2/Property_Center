@props(['Properties','propertyType' ,'count'=>1,'Locations'])
  <!-- Search Start -->
  <div class="container-fluid bg-primary mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px;">
            <div class="container">
                <div class="row g-2">
                    <div class="col-md-10">
                    <form action="{{route('search')}}"
                    method="get">
                    @csrf
                        <div class="row g-2">
                          
                            <div class="col-md-4">
                                <select class="form-select border-0 py-3" name="Property_type_id">
                                    <option selected>Property Type</option>
                                    @foreach($propertyType as $Property)
                                    <option value="{{$count++}}">{{$Property->name}}</option>
                                    @endforeach
                                   
                                </select>
                            </div>

                            
                            <div class="col-md-4">
                                <select class="form-select border-0 py-3" name="Purchase_Type">
                                    <option selected >Purchase Type</option>
                                    <option value="Sell">Buy</option>
                                    <option value="Rent">Rent</option>
                                    
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select class="form-select border-0 py-3" name="city_id">
                             
                                    <option selected>Location</option>
                                 
                                    @foreach($Locations as $Property)
                                   
                                    <option value="{{ $Property->id }}">{{ $Property->name  }}</option>
                                    @endforeach
                                   
                                </select>
                       
                            </div>
                            <div class="col-md-4">
                              
                                    <input type="number" class="form-select border-0 py-3" placeholder="Price from" name="price_from" id="price_to">
                                  
                       
                            </div>
                            <div class="col-md-4">
                              
                                    <input type="number" class="form-select border-0 py-3" placeholder="Price to" name="price_to" id="price_to">
                                  
                       
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-dark border-0 w-100 py-3">Search</button>
                    </div>

                        </form>
                </div>
            </div>
        </div>
        <!-- Search End -->