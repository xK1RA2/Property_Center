@props(['title'=>'Maps' , 'locations'])

<x-app-layout :$title>
<style>
    #map {
    height: 600px;
    width: 100%;
}
</style>

    <div id="map" class="w-10 h-10"></div>
   
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
          const locations = @json($locations); // The locations array

// Initialize the map, centered on the first location by default
const map = L.map('map').setView([locations[0].lat, locations[0].lng], 13);

// Add OpenStreetMap tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        zoom: 5,
        minZoom: 2,
        maxZoom: 20,
        noWrap: true,
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

// Add markers for all locations
locations.forEach(function(location) {
    L.marker([location.lat, location.lng]).addTo(map)
        .bindPopup( 'Price :'+ '$' + location.property.price);
});

// Optionally, fit the map bounds to the markers
let bounds = [];
locations.forEach(function(location) {
    bounds.push([location.lat, location.lng]);
});
if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            map.setView([userLat, userLng], 13);
            L.marker([userLat, userLng], { icon: L.icon({
                iconUrl: 'https://cdn.iconscout.com/icon/premium/png-256-thumb/user-location-2120090-1785431.png',
                iconSize: [40, 41],
                iconAnchor: [12, 41],
            }) }).addTo(map)
              .bindPopup('You are here')
              .openPopup();
           
        }, function () {
            // If permission denied or failed, fit bounds to all locations
            map.fitBounds(bounds);
        });
    } else {
        // Geolocation not supported
        map.fitBounds(bounds);
    }
map.fitBounds(bounds);
    </script>

</x-app-layout>
