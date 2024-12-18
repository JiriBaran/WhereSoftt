document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('map').setView([49.8175, 15.4730], 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let selectedCoords = null;

    map.on('click', (e) => {
        if (selectedCoords) {
            map.removeLayer(selectedCoords);
        }
        selectedCoords = L.marker(e.latlng).addTo(map);

        document.getElementById('latitude').value = e.latlng.lat.toFixed(5);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(5);
        document.getElementById('eventLocation').value = `Lat: ${e.latlng.lat.toFixed(5)}, Lng: ${e.latlng.lng.toFixed(5)}`;
    });

    const eventType = document.getElementById('eventType');
    const fieldNameDiv = document.getElementById('fieldNameDiv');

    eventType.addEventListener('change', function () {
        if (eventType.value === 'official') {
            fieldNameDiv.style.display = 'block';
        } else {
            fieldNameDiv.style.display = 'none';
        }
    });
});
