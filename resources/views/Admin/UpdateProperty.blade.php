@props(['Tailwind'=>1,'active'=>'Property','Property','Cities','PurchaseType','Purchases'])
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<x-guest-layout :$Tailwind>


<div class=" overflow-hidden flex flex-col  justify-center">
        <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

        <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-200">
            <div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
                class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden"></div>

            <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
                class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-[#00B98E] lg:translate-x-0 lg:static lg:inset-0">
               

                <x-Layouts.admin-header :$active />
            </div>
            <div class="flex flex-col flex-1 overflow-hidden">
                <header class="flex items-center justify-between px-6 py-4 bg-white border-b-4 border-[#00B98E]">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>

                        <div class="relative mx-4 lg:mx-0">



                        </div>
                    </div>

                    <div class="flex items-center">
                        <div x-data="{ notificationOpen: false }" class="relative">
                            <div x-show="notificationOpen" @click="notificationOpen = false"
                                class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                            <div x-show="notificationOpen"
                                class="absolute right-0 z-10 mt-2 overflow-hidden bg-white rounded-lg shadow-xl w-80"
                                style="width: 20rem; display: none;">
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Sara Salah</span> replied on the <span
                                            class="font-bold text-indigo-400" href="#">Upload Image</span> artical . 2m
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=634&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Slick Net</span> start following you . 45m
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1450297350677-623de575f31c?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=334&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Jane Doe</span> Like Your reply on <span
                                            class="font-bold text-indigo-400" href="#">Test with TDD</span> artical . 1h
                                    </p>
                                </a>
                                <a href="#"
                                    class="flex items-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-indigo-600">
                                    <img class="object-cover w-8 h-8 mx-1 rounded-full"
                                        src="https://images.unsplash.com/photo-1580489944761-15a19d654956?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=398&amp;q=80"
                                        alt="avatar">
                                    <p class="mx-2 text-sm">
                                        <span class="font-bold" href="#">Abigail Bennett</span> start following you . 3h
                                    </p>
                                </a>
                            </div>
                        </div>

                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = ! dropdownOpen"
                                class="relative block w-12 h-12 overflow-hidden rounded-full shadow focus:outline-none">
                                <img class="object-cover w-full h-full"
                                    src="https://images.unsplash.com/photo-1528892952291-009c663ce843?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=crop&amp;w=296&amp;q=80"
                                    alt="Your avatar">
                            </button>

                            <div x-show="dropdownOpen" @click="dropdownOpen = false"
                                class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                            <div x-show="dropdownOpen"
                                class="absolute right-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl"
                                style="display: none;">
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#00B98E] hover:text-white">Profile</a>

                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-[#00B98E] hover:text-white">Logout</a>
                            </div>
                        </div>
                    </div>
                </header>




                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                    <div class="container px-6 py-8 mx-auto">
                      
                    <div class="container mt-5">
    <div class="card shadow-lg p-4 border-green-500">
    <form action="{{route('property.update',$Property)}}" method="POST"  enctype="multipart/form-data">
        <h3 class="text-center mb-4 fs-4 ">Edit Property</h3>
       @csrf
            <div class="row g-3">
                   <!--  اسم الحي للسكن *required-->
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
                  <!-- المدينة للسكن-->
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
             
                    <!-- نوع العقار-->
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
              
                <!-- سعر ومساحة السكن-->
                <div class="col-md-6">
                    <label class="form-label">Price($)</label>
                    <input type="number" value="{{$Property->price }}" name="price" class="form-control shadow-sm"placeholder="Enter price"required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Area(m²)</label>
                    <input type="number" name="area" value="{{$Property->Features->Area}}"  class="form-control shadow-sm" placeholder="Enter area"min="100"required>
                </div>
 
             
          
              
                <!-- عدد الغرف في البيت-->
                <div class="col-md-6">
                    <label class="form-label">Bedrooms</label>
                    <input type="number" name="Bedrooms" value="{{$Property->Features->Bedrooms}}" class="form-control shadow-sm"placeholder="Number of Bedrooms"min="1"required>
                </div>
                <!-- عدد الحمامات في الشقة-->
                <div class="col-md-6">
                    <label class="form-label">Bathrooms</label>
                    <input type="number" name="Bathrooms" value="{{$Property->Features->Bathrooms}}" class="form-control shadow-sm"placeholder="Number of Bathrooms"min="1"required>
                </div>
                <!-- عدد الصالات في السكن-->
                <div class="col-md-6">
                    <label class="form-label">Living Rooms</label>
                    <input type="number"name="rooms" value="{{$Property->Features->Rooms}}" class="form-control shadow-sm"placeholder="Number of Living Rooms"min="1"required>
                </div>

                <!-- عدد المطابخ وخيار البيع أو الإيجار -->
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
                <!-- الوصف للسكن او العقار-->
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea class="form-control shadow-sm"rows="4" value="{{$Property->description}}" name="Description">{{$Property->description}}</textarea>
                </div>
                <!-- وسائل الراحة واضافات السكن -->
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
               <!-- ارفع صور على الموقع-->
                <div class="col-md-12 text-center  ">
                    <label class="form-label d-block">Upload Property Images</label>
                    <input type="file" name="images[]" multiple  name="images[]" id="fileInput"class="d-none  "  />
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

                    </div>
                </main>
            </div>
        </div>
    </div>

</x-guest-layout>
