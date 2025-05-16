@props(['Tailwind'=>0,'title'=>"Dealers",'Dealers'])

<x-app-layout :$Tailwind :$title>
<div class="container py-5">
        <h2 class="text-center mb-4">List of Traders</h2>
        <div>
            <div class="row pt-4">
            
                 @foreach($Dealers as $Dealer)
                <div class="col-md-3 mb-4">
                      <a href="{{ route('Dealer_Profile',$Dealer) }}"class="text-decoration-none"><!--الرابط حط فيه البروفايل تاعه--> 
                        <div class="card shadow-sm border-0">
                            <img  src="{{
                            asset('storage/'.$Dealer->profile_image) 
                            }}"  class="card-img-top"alt="Trader Image"style="height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title text-dark">{{$Dealer->name}}</h5>
                                <p class="card-text text-muted">{{$Dealer->email}}</p>
                                <p class="text-muted">{{$Dealer->phone}}</p>
                                <div class="text-warning">
                                @if(request()->user()->role_id==2)
                    
                    
<div class=" text-warning ">
                    <?php $i=1 ;
                    $Avg = $Dealer->Rate->avg('Rate'); ?>
                    @for($i; $i<=$Avg ; $i++)
                       <span > <i class="fa-solid fa-star"></i> </span>
                    @endfor
                    
                    @if($i - $Avg < 1 )
                    <i class="fa-solid fa-star-half-stroke"></i>
                    @endif
                    </div>
 

              @endif
              
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</x-app-layout>
