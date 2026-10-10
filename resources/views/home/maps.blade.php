@props(['title'=>'Maps' , 'locations'])

<x-app-layout :$title>
<style>
    #map {
    height: 600px;
    width: 100%;
}
</style>
    <div class=" m-2 mx-5 border border-primary">
    <div id="map" class="w-10 h-10"></div>
   </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
          const locations = @json($locations);

const map = L.map('map').setView([locations[0].lat, locations[0].lng], 2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        zoom: 5,
        minZoom: 2,
        maxZoom: 20,
        noWrap: true,
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

locations.forEach(function(location) {
  
    L.marker([location.lat, location.lng]).addTo(map)
        .bindPopup( 'Price :'+ '$' + location.property.price + '<br>');
});

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
            map.fitBounds(bounds);
        });
    } else {
        map.fitBounds(bounds);
    }
map.fitBounds(bounds);
    </script>

</x-app-layout>
