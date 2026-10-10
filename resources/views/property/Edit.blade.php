@props(['title'=>"EditProperty" , 'Property','Cities' , 'propertyType','Purchases'])
<x-app-layout :$title >
<div class="container mt-5">
    <div class="card shadow-lg p-4 border-primary">
    <form action="{{route('property.update',$Property)}}" method="POST"  enctype="multipart/form-data">
        <h3 class="text-center mb-4">Edit Property</h3>
       @csrf
            <div class="row g-3">
                   <div class="col-md-6">
                  <label class="form-label">State</label>
                  <select id="citySelect"  class="form-control bg-white"name="state_id">
                 
                  <option value="{{ $Property->City->state->id }}">{{ $Property->City->state->name }}</option>
    
                  @foreach ($Cities as $city)
                      <option value="{{$city->state->id}}"  >
                          {{$city->state->name}}
                      </option>
                  @endforeach
                  </select>
                   </div>
                  <div class="col-md-6">
                  <label class="form-label">City</label>
                  <select id="citySelect"  class="form-control bg-white"name="city_id">
                 
                  <option value="{{$Property->City->id}}">{{ $Property->City->name }}</option>
    
                  @foreach ($Cities as $city)
                      <option value="{{$city->id}}"  >
                          {{$city->name}}
                      </option>
                  @endforeach
                  </select>
                   </div>
             
                <div class="col-md-6">
                  <label class="form-label">PropertyType</label>
                  <select id="citySelect"  class="form-control bg-white" name="PropertyName" >
                 
                  <option value="{{$Property->PropertyType->id}}">{{ $Property->PropertyType->name }}</option>
    
                    @foreach($propertyType as $Type)
                    <?php if($Property->PropertyType->name == $Type->name) continue; ?>
                      <option value="{{$Type->id}}"  >
                          {{$Type->name}}
                      </option>
                    @endforeach           
                  
                  </select>
                   </div>
              
                <div class="col-md-6">
                    <label class="form-label">Price($)</label>
                    <input type="number" value="{{$Property->price }}" name="price" class="form-control shadow-sm"placeholder="Enter price"required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Area(m²)</label>
                    <input type="number" name="area" value="{{$Property->Features->Area}}"  class="form-control shadow-sm" placeholder="Enter area"min="100"required>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ old('address', $Property->address) }}" maxlength="255" class="form-control shadow-sm" placeholder="Street, building">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year built</label>
                    <input type="number" name="year" value="{{ old('year', $Property->year) }}" min="1800" max="2031" class="form-control shadow-sm">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Bedrooms</label>
                    <input type="number" name="Bedrooms" value="{{$Property->Features->Bedrooms}}" class="form-control shadow-sm"placeholder="Number of Bedrooms"min="1"required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Bathrooms</label>
                    <input type="number" name="Bathrooms" value="{{$Property->Features->Bathrooms}}" class="form-control shadow-sm"placeholder="Number of Bathrooms"min="1"required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Living Rooms</label>
                    <input type="number"name="rooms" value="{{$Property->Features->Rooms}}" class="form-control shadow-sm"placeholder="Number of Living Rooms"min="1"required>
                </div>

                        <div class="col-md-6">
                            <label class="form-label">Kitchens</label>
                            <input type="number"name="Kitchen" value="{{$Property->Features->Kitchen}}" class="form-control shadow-sm"placeholder="Number of kitchen"min="0"required>
                        </div>
                        <div class="col-md-6">

                            <label class="form-label">Sale/Rent</label>
                            
                            <select class="form-select shadow-sm"required name="PropertyPurchase">
                            <option value="{{$Property->PurchaseType}}">For {{$Property->PurchaseType}}</option>
                             
                            @foreach($Purchases as $Purchase)
                              <?php if($Property->PurchaseType == $Purchase->PurchaseType) continue; ?>
                                <option value="{{$Purchase->PurchaseType}}">For {{$Purchase->PurchaseType}}</option>
                                @endforeach
                            </select>
                        </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea class="form-control shadow-sm"rows="4" value="{{$Property->description}}" name="Description">{{$Property->description}}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Features</label>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input"type="checkbox"id="amenity1"name="Air_Conditioner" value="1" <?php if($Property->Features->Air_Conditioner==1){ ?> checked <?php }?>>
                                <label class="form-check-label"for="amenity1">Air Conditioning</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"type="checkbox"id="amenity2"name="Parking"value="1"<?php if($Property->Features->Parking==1){ ?> checked <?php }?>>
                                <label class="form-check-label"for="amenity2">Parking</label>
                            </div>
                        </div>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input"type="checkbox"id="amenity4"name="Heating"value="1"<?php if($Property->Features->Heating==1){ ?> checked <?php }?>>
                                <label class="form-check-label"for="amenity4">Heating</label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mt-5 text-dark">
                <div class="col-md-12 text-center  ">
                    <label class="form-label d-block">Upload Property Images</label>
                    <input type="file" name="images[]" multiple  name="images[]" id="fileInput"class="d-none  "  />
                    <label for="fileInput"class="btn btn-success btn-lg d-flex align-items-center justify-content-center mx-auto p-0 shadow-sm border rounded-circle"style="width: 70px; height: 70px; border-radius: 0;">
                    <i class="fa-solid fa-plus"></i>
                    </label>
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit"class="btn btn-success w-100 shadow-sm">Submit Property</button>
            </div>
</form>
    </div>
</div>
<script src="./assets/js/bootstrap.bundle.min.js"></script>
</x-app-layout  >

