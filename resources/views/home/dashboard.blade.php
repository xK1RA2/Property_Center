@props(['title'=>'Dashboard'])

<x-app-layout :$title>

<div class="container mt-5">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 class="header-title">Residential Real Estate Dashboard</h1>
            <p class="text-muted">Manage Your Properties with Ease and Style</p>
        </div>

        <!--  Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card stat-card p-3">
                    <div class="card-body d-flex align-items-center">
                        <i class="bi bi-house stat-icon text-primary me-3"></i>
                        <div>
                            <h6 class="card-title text-muted mb-1">Total Properties</h6>
                            <h3 class="card-text text-primary fw-bold">245</h3>
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
                            <h3 class="card-text text-success fw-bold">180</h3>
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
                            <h3 class="card-text text-warning fw-bold">50</h3>
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
                            <h3 class="card-text text-info fw-bold">$12.5M</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- الجدول -->
        <div class="card table-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Recently Added Properties</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Property Name</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Date Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Yasmin Villa</td>
                                <td>Amman</td>
                                <td>$250,000</td>
                                <td><span class="property-status bg-success text-white">Sold</span></td>
                                <td>2025-03-01</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Marj Apartment</td>
                                <td>Irbid</td>
                                <td>$85,000</td>
                                <td><span class="property-status bg-warning text-dark">For Sale</span></td>
                                <td>2025-03-02</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Nakheel House</td>
                                <td>Aqaba</td>
                                <td>$120,000</td>
                                <td><span class="property-status bg-info text-white">Reserved</span></td>
                                <td>2025-03-03</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

       

</x-app-layout>
