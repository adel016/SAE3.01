<main class="index">
    <section class="weather-info">
        <div class="weather-left">
            <h1>Paris, France</h1>
            <p>À 15h00</p>
            <div class="temperature">
                <span class="temp-value">6°</span>
                <img src="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/img/ACCUEIL/nuage_soleil.avif" alt="Nuageux" class="meteo">
            </div>
            <div class="weather-details">
                <h2>Un peu Nuageux</h2>
                <p>3% de chance de pluie jusqu'à 9:00</p>
                <ul>
                    <li><span class="icon">🌡</span> Max/Min: 9°/3°</li>
                    <li><span class="icon">💧</span> Humidité: 61%</li>
                    <li><span class="icon">🌬</span> Vent: 24 Km/h</li>
                </ul>
            </div>
        </div>
        <div class="map-container">
            <div id="map"></div>
        </div>
    </section>
</main>



<script>
const map = L.map('map');

// Ajuster la carte pour afficher toute la France
const franceBounds = [
    [51.124199, -5.142222], // Nord-Ouest de la France
    [41.325300, 9.662499]  // Sud-Est de la France
];

map.fitBounds(franceBounds); // Adapter les limites à la France

// Ajouter des tuiles de fond
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
}).addTo(map);

// Charger et afficher les régions depuis le GeoJSON
fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/gjson/regions.geojson')
    .then((response) => response.json())
    .then((geojsonData) => {
        L.geoJSON(geojsonData, {
            onEachFeature: (feature, layer) => {
                layer.on('mouseover', () => {
                    layer.setStyle({
                        color: 'lightgreen',
                        weight: 2,
                        fillColor: 'lightgreen',
                        fillOpacity: 0.7,
                    });
                });
                layer.on('mouseout', () => {
                    layer.setStyle({
                        color: '#004080',
                        weight: 1,
                        fillColor: '#0077be',
                        fillOpacity: 0.5,
                    });
                });
                layer.bindPopup(`Région : ${feature.properties.name}`);
            },
            style: {
                color: '#004080',
                weight: 1,
                fillColor: '#0077be',
                fillOpacity: 0.5,
            },
        }).addTo(map);
    })
    .catch((error) => console.error('Erreur lors du chargement ou du traitement du GeoJSON:', error));


</script>
</section>
</main>