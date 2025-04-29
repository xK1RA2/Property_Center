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
                            <img src="./images/JohnDoe.jpg" class="card-img-top"alt="Trader Image"style="height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title text-dark">{{$Dealer->name}}</h5>
                                <p class="card-text text-muted">{{$Dealer->email}}</p>
                                <p class="text-muted">{{$Dealer->phone}}</p>
                                <div class="text-warning">★★★☆☆</div>
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
