<h1>Bienvenue sur MeteoVision</h1>
<main class="index">
    <section class="weather-info">
        <!-- Section gauche (infos météo) -->
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

        <div class="carte">
            <!-- Barre de recherche -->
            <div class="region-search">
                <label for="regionInput">Rechercher une région :</label>
                <input type="text" id="regionInput" placeholder="Entrez le nom de la région">
                <button id="searchRegionButton">Rechercher</button>
            </div>

            <!-- Carte interactive -->
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
    </section>
</main>


<script>
// INITIALISATION DE LA CARTE
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
        // Ajouter le GeoJSON des régions
        const geojsonLayer = L.geoJSON(geojsonData, {
            onEachFeature: (feature, layer) => {
                // Gérer le survol
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
                layer.bindPopup(`Région : ${feature.properties.nom}`);
            },
            style: {
                color: '#004080',
                weight: 1,
                fillColor: '#0077be',
                fillOpacity: 0.5,
            },
        }).addTo(map);

        // Charger les stations
        fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=getStationJSON&controller=station')
            .then((response) => response.json())
            .then((stations) => {
                // Ajouter les stations à la carte
                stations.forEach((station) => {
                    const { latitude, longitude, ville, region, altitude } = station;

                    // Trouver la région correspondante dans le GeoJSON
                    const matchingRegion = geojsonData.features.find(
                        (feature) => feature.properties.nom === region
                    );

                    if (matchingRegion) {
                        // Ajouter un marqueur pour la station
                        L.marker([latitude, longitude])
                            .addTo(map)
                            .bindPopup(
                                `<strong>${ville}</strong><br>
                                Région: ${region}<br>
                                Altitude: ${altitude}m`
                            );
                    }
                });
            })
            .catch((error) => console.error('Erreur lors du chargement des stations:', error));
    })
    .catch((error) =>
        console.error('Erreur lors du chargement ou du traitement du GeoJSON:', error)
    );
</script>

<script>
// RECHERCHE DE LA REGION SUR LA CARTE
document.getElementById('searchRegionButton').addEventListener('click', () => {
    const regionInput = document.getElementById('regionInput').value.trim();

    if (!regionInput) {
        alert('Veuillez entrer le nom d\'une région.');
        return;
    }

    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/gjson/regions.geojson')
        .then((response) => response.json())
        .then((geojsonData) => {
            // Rechercher la région par son nom
            const matchingRegion = geojsonData.features.find(
                (feature) => feature.properties.nom.toLowerCase() === regionInput.toLowerCase()
            );

            if (matchingRegion) {
                // Centrer la carte sur la région trouvée
                const regionBounds = L.geoJSON(matchingRegion).getBounds();
                map.fitBounds(regionBounds);

                // Optionnel : Ajouter une bordure ou un effet visuel
                L.geoJSON(matchingRegion, {
                    style: {
                        color: 'red',
                        weight: 3,
                        fillColor: '#f03',
                        fillOpacity: 0.2,
                    },
                }).addTo(map);

                alert(`La carte a été centrée sur la région : ${matchingRegion.properties.nom}`);
            } else {
                alert('Région non trouvée. Veuillez vérifier le nom.');
            }
        })
        .catch((error) => {
            console.error('Erreur lors de la recherche de la région :', error);
            alert('Erreur lors de la recherche de la région.');
        });
});
</script>


</section>
</main>