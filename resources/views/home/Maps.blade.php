@props(['title'=>'Maps'])

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
        const map = L.map('map').setView([51.505, -0.09], 13); // Example: London

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([51.505, -0.09]).addTo(map)
            .bindPopup('A sample marker.')
            .openPopup();
    </script>

</x-app-layout>
