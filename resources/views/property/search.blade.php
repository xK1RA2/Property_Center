  
  @props(['title'=>'View','Properties','propertyType','Locations'])

<x-app-layout :$title>

<div class="container-xxl py-5">
            <div class="container">
                <div class="row g-0 gx-5 align-items-end">
                    <div class="col-lg-6">
                       
                    </div>
                    <div class="col-lg-6 text-start text-lg-end wow slideInRight" data-wow-delay="0.1s">
                    
                    </div>
         </div>
         <div>
        
         <x-search-form :$Locations :$Properties :$propertyType action='/search' method="Get"/>
      

         <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                         
         @foreach($Properties as $Property)     
              
        <x-property-item :$Property  :isInWishList="$Property->favouriteProperties->contains(\Illuminate\Support\Facades\Auth::user())" />
        @endforeach
         </div>
   </div>
       </div>

</div>
   </div>
       </div>

                                   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-app-layout >
