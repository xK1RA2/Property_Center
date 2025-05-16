
@props(['title'=>'Dashboard' , 'Properties','Delaer','Requests','Checkouts'])

<x-app-layout >

<div class="container mt-5">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="header-title">Residential Real Estate Dashboard</h1>
        </div>

        <!--  Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-house stat-icon text-primary me-3"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">Total Properties</h6>
                            <h3 class="card-text text-primary fw-bold">{{$Properties->count()}}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-check-circle stat-icon text-success me-3"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">Sold Properties</h6>
                            <h3 class="card-text text-success fw-bold">{{ $Checkouts->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-hourglass-split stat-icon text-warning me-3"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">For Sale</h6>
                                <?php $count =0; ?>
                            <h3 class="card-text text-warning fw-bold">@foreach ($Properties as $Property )
                                @if($Property->status == "Available")
                                <?php $count++ ?>
                            @endif
                            @endforeach
                            {{ $count }}
                        </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-currency-dollar stat-icon text-info me-3"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">Revenue</h6>
                            <h3 class="card-text text-info fw-bold">${{$Checkouts->sum('price')}}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- الجدول -->
        @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if(session('Rejected'))
                    <div class="alert alert-danger">
                        {{ session('Ruccess') }}
                    </div>
                    @endif
        <div class="card table-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 text-white">Preview Requests</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                  
                    <table class="table table-hover mb-0 table-bordered">
                        <thead class="text-dark">
                            <tr>
                                <th>#</th>
                                <th>Property Type</th>
                                <th>Username</th>
                                <th>User Phone</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Additional Notes</th>
                                <th>Action</th>
                               
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <?php $count =0; ?>
                                @foreach ($Requests as $Req)
                                <?php $count++ ?>
                                <td>{{ $count }}</td>
                                <td><a href="{{ route('property.details',$Req->Property) }}">{{$Req->Property->PropertyType->name}}</a></td>
                                <td>{{$Req->User->name}}</td>
                                <td>{{$Req->User->phone}}</td>
                                <td>{{$Req->date}}</td>
                                <td class="@if($Req->status =='Approved')text-primary @elseif($Req->status =='Rejected') text-danger @endif ">{{$Req->status}}</td>
                               
                                <td>{{$Req->description}}</td>
                                @if($Req->status=="Pending" )
                                <td><a class="btn btn-success text-white" href="{{ route('BookAccept',$Req) }}">Accept</a>
                                <a class="btn btn-danger text-white" href="{{ route('BookRefuse',$Req) }}">Refuse</a></td>
                               @else
                               <td></td>
                                @endif
                            </tr>
                            @endforeach
 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

       


</x-app-layout>