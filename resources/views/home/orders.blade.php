@props(['Tailwind'=>0,'title'=>'Orders','orders'])
<x-app-layout :$title :$Tailwind>
<div class="container py-5">
        <h2 class="text-center mb-4 text-dark fw-bold">My Orders</h2>
        <div class="table-responsive">
            <table class="table  table-hover text-center shadow-sm rounded table-bordered border-primary">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Dealer Name</th>
                        <th>PropertyType</th>
                        <th>Location</th>
                        <th>Price</th>
                     
                        <th>Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    
                    @foreach ($orders as $order)
                   
                    <tr>
                       
                        <td class="align-middle">{{$order->id}}</td>
                        <td><img src="./assets/images/download.jpg" alt="Property" class="img-fluid rounded" style="max-width: 100px;"></td>
                        <td class="align-middle">{{$order->Property->Dealer->name}}</td>
                        <td class="align-middle">{{$order->Property->PropertyType->name}}</td>
                        <td class="align-middle">{{$order->Property->city->state->name}}</td>
                        <td class="align-middle text-success fw-bold">{{$order->Property->price}} $</td>
                        <td class="align-middle">{{$order->created_at}}</td>
                        <td class="align-middle"><a href="{{ route('property.details',$order->Property) }}" class="btn btn-outline-primary btn-sm">View</a></td>
                    </tr>
                    @endforeach
              
                  
                </tbody>
            </table>
        </div>
    </div></x-app-layout>
