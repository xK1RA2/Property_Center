@props(['Properties','propertyType' ,'count'=>0])
  <!-- Search Start -->
  <div class="container-fluid bg-primary mb-5 wow fadeIn" data-wow-delay="0.1s" style="padding: 35px;">
            <div class="container">
                <div class="row g-2">
                    <div class="col-md-10">
                    <form action="View/search"
                    method="GET">
                        <div class="row g-2">
                          
                            <div class="col-md-4">
                                <select class="form-select border-0 py-3">
                                    <option selected>Property Type</option>
                                    @foreach($propertyType as $Property)
                                    <option value="{{$count++}}">{{$Property->name}}</option>
                                    @endforeach
                                   
                                </select>
                            </div>

                            
                            <div class="col-md-4">
                                <select class="form-select border-0 py-3">
                                    <option selected>Purchase Type</option>
                                    <option value="1">Buy</option>
                                    <option value="2">Rent</option>
                                    
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select class="form-select border-0 py-3">
                                    <option selected>Location</option>
                                    @foreach($Properties as $Property)
                                    <option value="{{$Property->city->state->id}}">{{$Property->city->state->name}}</option>
                                    @endforeach
                               
                                </select>
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