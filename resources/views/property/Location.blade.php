

<x-app-layout >

<style>  #map {
            height: 400px;
            width: 70%;
        }</style>

@if(session('success'))
<div class="alert alert-success pt-2 mt-2 mx-2">
    {{ session('success') }}
</div>
    @endif
<div class="container d-flex  space-x-5 mt-5 flex-wrap">

    <div class=" text-center   me-5 border border-primary rounded p-3 ">
   
    <h2 class="py-4  ">Add Location</h2>
<form method="POST" class=""  action="{{ route('locations.store') }}">
    @csrf
  
    <label>Latitude:</label>
    <input type="text" name="lat" id="lat" readonly required readonly><br><br>

    <label>Longitude:</label>
    <input type="text" name="lng" id="lng" readonly required readonly><br><br>

    <button type="submit" class="btn btn-primary">Save Location</button>
    
</form>
</div>
<div id="map" ></div>

</div>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([39.8283, -98.5795], 4); // Center USA

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        noWrap: true,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Set form values
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;

        // Remove old marker
        if (marker) {
            map.removeLayer(marker);
        }

        // Add new marker
        marker = L.marker([lat, lng]).addTo(map);
    });
</script>


</x-app-layout>