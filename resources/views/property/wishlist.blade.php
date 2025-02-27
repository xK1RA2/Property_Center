@props(['Properties' , 'title'=>'Wishlist'])

<x-app-layout :$title>
<main>
        <!-- New Cars -->
        <section>
            <div class="container pt-10">
              <div class="flex justify-between items-center ">
                  <h2 class=" py-5">My Favourite Properties</h2>
              </div>

              <div >
              <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                          
              @foreach($Properties as $Property)
             
                        <x-property-item :$Property :isInWishList="true"/>
                        @endforeach
</div>
</div>
</div>
              </div>

            </div>
        
        </section>
        <!--/ New Cars -->
      </main>
</x-app-layout>
