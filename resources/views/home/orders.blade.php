@props(['Tailwind'=>0,'title'=>'Orders'])
<x-app-layout :$title :$Tailwind>
<div class="container py-5">
        <h2 class="text-center mb-4">My Orders</h2>
        <div class="table-responsive">
            <table class="table table-striped text-center">
                <thead class="table-success">
                    <tr>
                        <!-- معلومات جدولي -->
                        <th>#</th>
                        <th>Image</th>
                        <th>Dealer Name</th>
                        <th>Property Name</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Order Status</th>
                        <th>Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- اول عقار عنا -->
                         <!-- شيل الواحد اذا بدك -->
                        <td>1</td>
                        <td><img src="./assets/images/home.webp"alt="Property"class="img-fluid rounded"style="max-width: 100px;"></td>
                        <td>John Doe</td>
                        <td>Luxury Villa</td>
                        <td>Irbid</td>
                        <td>400,000 $</td>
                        <td><span class="badge bg-success">Approved</span></td>
                        <td>2025-02-20</td>
                        <!-- حط رابطه -->
                        <td><a href=""class="btn btn-success btn-md">View</a></td>
                    </tr>
                    <tr>
                        <!-- ثاني عقار -->
                        <td>2</td>
                        <td><img src="./assets/images/home.webp"alt="Property"class="img-fluid rounded"style="max-width: 100px;"></td>
                        <td>Jane Smith</td>
                        <td>Residential Apartment</td>
                        <td>Zarqa</td>
                        <td>75,000 $</td>
                        <td><span class="badge bg-warning">Under Review</span></td>
                        <td>2025-02-18</td>
                        <!-- رابطه بالداتا -->
                        <td><a href=""class="btn btn-success btn-md">View</a></td>
                    </tr>
                    <tr>
                        <!-- الثالث -->
                        <td>3</td>
                        <td><img src="./assets/images/home.webp"alt="Property"class="img-fluid rounded"style="max-width: 100px;"></td>
                        <td>Mike Johnson</td>
                        <td>office</td>
                        <td>Amman</td>
                        <td>30,000 $</td>
                        <td><span class="badge bg-danger">Rejected</span></td>
                        <td>2025-02-15</td>
                        <!-- الرابط -->
                        <td><a href=""class="btn btn-success btn-md">View</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</x-app-layout>
