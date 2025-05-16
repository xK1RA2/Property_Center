@props(['title'=>"AddProperty" , 'Cities' , 'propertyType','Purchases'])
<x-app-layout :$title >
<div class="container mt-5">
    <div class="card shadow-lg p-4 border-primary">
    <form action="{{route('property.store')}}" method="POST"  enctype="multipart/form-data">
        <h3 class="text-center mb-4">Add Property</h3>
       @csrf
            <div class="row g-3">
                   <!--  اسم الحي للسكن *required-->
                   <div class="col-md-6">
                  <label class="form-label">State</label>
                  <select id="citySelect"  class="form-control bg-white"name="state_id">
                 
                  <option value="">City</option>
    
                  @foreach ($Cities as $city)
                      <option value="{{$city->state->id}}"  >
                          {{$city->state->name}}
                      </option>
                  @endforeach
                  </select>
                   </div>
                  <!-- المدينة للسكن-->
                  <div class="col-md-6">
                  <label class="form-label">City</label>
                  <select id="citySelect"  class="form-control bg-white"name="city_id">
                 
                  <option value="">City</option>
    
                  @foreach ($Cities as $city)
                      <option value="{{$city->id}}"  >
                          {{$city->name}}
                      </option>
                  @endforeach
                  </select>
                   </div>
             
                    <!-- نوع العقار-->
                <div class="col-md-6">
                  <label class="form-label">PropertyType</label>
                  <select id="citySelect"  class="form-control bg-white" name="PropertyName">
                 
                 
                    @foreach($propertyType as $Type)
                
                      <option value="{{$Type->id}}"  >
                          {{$Type->name}}
                      </option>
                    @endforeach           
                  
                  </select>
                   </div>
              
                <!-- سعر ومساحة السكن-->
                <div class="col-md-6">
                    <label class="form-label">Price($)</label>
                    <input type="number" value="old('price')" name="price" class="form-control shadow-sm"placeholder="Enter price"required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Area(m²)</label>
                    <input type="number" name="area" value="old('area')"  class="form-control shadow-sm" placeholder="Enter area"min="100"required>
                </div>
 
             
          
              
                <!-- عدد الغرف في البيت-->
                <div class="col-md-6">
                    <label class="form-label">Bedrooms</label>
                    <input type="number" name="Bedrooms" value="old('Bedrooms')" class="form-control shadow-sm"placeholder="Number of Bedrooms"min="1"required>
                </div>
                <!-- عدد الحمامات في الشقة-->
                <div class="col-md-6">
                    <label class="form-label">Bathrooms</label>
                    <input type="number" name="Bathrooms" value="old('Bathrooms')" class="form-control shadow-sm"placeholder="Number of Bathrooms"min="1"required>
                </div>
                <!-- عدد الصالات في السكن-->
                <div class="col-md-6">
                    <label class="form-label">Living Rooms</label>
                    <input type="number"name="rooms" value="old('rooms')" class="form-control shadow-sm"placeholder="Number of Living Rooms"min="1"required>
                </div>

                <!-- عدد المطابخ وخيار البيع أو الإيجار -->
                        <div class="col-md-6">
                            <label class="form-label">Kitchens</label>
                            <input type="number"name="Kitchen" value="old('Kitchen')" class="form-control shadow-sm"placeholder="Number of kitchen"min="0"required>
                        </div>
                        <div class="col-md-6">

                            <label class="form-label">Sale/Rent</label>
                            
                            <select class="form-select shadow-sm"required name="PropertyPurchase">
                               
                            @foreach($Purchases as $Purchase)
                          
                                <option value="{{$Purchase->PurchaseType}}">For {{$Purchase->PurchaseType}}</option>
                                @endforeach
                            </select>
                        </div>
                <!-- الوصف للسكن او العقار-->
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea class="form-control shadow-sm"rows="4"value="old('Description')"placeholder="Enter property description"name="Description"></textarea>
                </div>
                <!-- وسائل الراحة واضافات السكن -->
                <div class="col-md-6">
                    <label class="form-label">Features</label>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input"type="checkbox"id="amenity1"name="Air_Conditioner"value="1">
                                <label class="form-check-label"for="amenity1">Air Conditioning</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"type="checkbox"id="amenity2"name="Parking"value="1">
                                <label class="form-check-label"for="amenity2">Parking</label>
                            </div>
                        </div>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input"type="checkbox"id="amenity4"name="Heating"value="1">
                                <label class="form-check-label"for="amenity4">Heating</label>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="mt-5 text-dark">
               <!-- ارفع صور على الموقع-->
                <div class="col-md-12 text-center  ">
                    <label class="form-label d-block">Upload Property Images</label>
                    <input type="file" name="images[]" multiple  name="images[]" id="fileInput"class="d-none  " required />
                    <label for="fileInput"class="btn btn-success btn-lg d-flex align-items-center justify-content-center mx-auto p-0 shadow-sm border rounded-circle"style="width: 70px; height: 70px; border-radius: 0;">
                    <i class="fa-solid fa-plus"></i>
                    </label>
                </div>
            </div>
            <!-- ارسال المعلومات للداتا بيس-->
            <div class="text-center mt-4">
                <button type="submit"class="btn btn-success w-100 shadow-sm">Submit Property</button>
            </div>
</form>
    </div>
</div>
<script src="./assets/js/bootstrap.bundle.min.js"></script>
</x-app-layout  >



