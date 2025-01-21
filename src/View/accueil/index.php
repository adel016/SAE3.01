<h1>Bienvenue sur MeteoVision</h1>
<main class="index">
    <section class="weather-info">
        <!-- Section gauche (infos météo de la région et des stations) -->
        <div class="weather-left">
            <h1 id="region-name">Nom de la région</h1>
            <p id="current-time"></p>
            <div class="temperature">
                <span id="temperature" class="temp-value">--°</span>
                <img id="weather-icon" src="#" alt="Conditions météo" class="meteo">
            </div>
            <div class="weather-details">
                <h2 id="weather-condition">--</h2>
                <p id="rain-chance">--% de chance de pluie</p>
                <ul id="weather-stats">
                    <li><span class="icon">🌡</span> Max/Min: --°/--°</li>
                    <li><span class="icon">💧</span> Humidité: --%</li>
                    <li><span class="icon">🌬</span> Vent: -- Km/h</li>
                </ul>
            </div>
            <div class="stations-list">
                <h3>Stations dans la région :</h3>
                <ul id="stations-list">
                    <li>Aucune station disponible</li>
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
const map = L.map('map').setView([46.603354, 1.888334], 6); // Centré sur la France
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
}).addTo(map);

// Variables pour stocker les données
let geojsonData; // Données des régions
let stationsData; // Données des stations
let geojsonLayer; // Stocker le layer GeoJSON pour réinitialisation
const stationMarkers = []; // Liste des marqueurs pour nettoyage

// Région par défaut
const defaultRegionName = "Île-de-France";

// Charger les données des régions et stations
Promise.all([
    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/gjson/regions.geojson').then(res => res.json()),
    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=getSynopData&controller=api').then(res => res.json())
])
    .then(([regions, stations]) => {
        geojsonData = regions;
        stationsData = stations;

        // Afficher les régions sur la carte
        geojsonLayer = L.geoJSON(geojsonData, {
            style: {
                color: '#004080',
                weight: 1,
                fillColor: '#0077be',
                fillOpacity: 0.5,
            },
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
                layer.bindPopup(`Région : ${feature.properties.nom}`);
            }
        }).addTo(map);

        // Afficher la région par défaut
        showRegionData(defaultRegionName);
    })
    .catch(error => {
        console.error('Erreur lors du chargement des données:', error);
        alert('Erreur lors du chargement des données de la carte.');
    });

// Fonction pour afficher les données météo et stations d'une région
function showRegionData(regionName) {
    // Réinitialiser les marqueurs des stations
    stationMarkers.forEach(marker => map.removeLayer(marker));
    stationMarkers.length = 0; // Vide la liste des marqueurs

    // Effacer les styles de la carte pour une nouvelle recherche
    if (geojsonLayer) map.removeLayer(geojsonLayer);
    geojsonLayer = L.geoJSON(geojsonData, {
        style: {
            color: '#004080',
            weight: 1,
            fillColor: '#0077be',
            fillOpacity: 0.5,
        },
    }).addTo(map);

    // Recherche dans les régions
    const matchingRegion = geojsonData.features.find(
        feature => feature.properties.nom.toLowerCase() === regionName.toLowerCase()
    );

    if (matchingRegion) {
        const regionBounds = L.geoJSON(matchingRegion).getBounds();
        map.fitBounds(regionBounds);

        L.geoJSON(matchingRegion, {
            style: {
                color: 'red',
                weight: 3,
                fillColor: '#f03',
                fillOpacity: 0.2,
            },
        }).addTo(map);

        // Calculer les données météo (moyennes, min, max) pour la région
        const stationsInRegion = stationsData.filter(
            (station) => station.region.toLowerCase() === regionName.toLowerCase()
        );

        const weatherData = calculateWeatherData(stationsInRegion);

        // Ajouter les stations dans la région
        stationsInRegion.forEach(station => {
            const { latitude, longitude, ville, region, altitude, temp, humidity, windSpeed } = station;
            const marker = L.circleMarker([latitude, longitude], {
                radius: 8,
                color: '#004080',
                fillColor: '#0077be',
                fillOpacity: 0.7,
            }).addTo(map);

            // Ajouter les informations dans un popup
            marker.bindPopup(`
                <strong>${ville}</strong><br>
                Région: ${region}<br>
                Température: ${temp}°C<br>
                Humidité: ${humidity}%<br>
                Vent: ${windSpeed} km/h<br>
                Altitude: ${altitude || 'Non spécifiée'}m
            `);

            stationMarkers.push(marker); // Ajouter le marqueur à la liste pour nettoyage futur
        });

        updateWeatherData(regionName, weatherData, stationsInRegion);
    } else {
        alert('Aucune région trouvée. Veuillez vérifier le nom.');
    }
}

// Fonction pour calculer les moyennes et extrêmes météo d'une région
function calculateWeatherData(stations) {
    const temps = stations.map(station => station.temp).filter(temp => temp !== '--');
    const humidities = stations.map(station => station.humidity).filter(humidity => humidity !== '--');
    const windSpeeds = stations.map(station => station.windSpeed).filter(windSpeed => windSpeed !== '--');

    const avg = arr => arr.length > 0 ? (arr.reduce((a, b) => a + b, 0) / arr.length).toFixed(1) : '--';
    const min = arr => arr.length > 0 ? Math.min(...arr).toFixed(1) : '--';
    const max = arr => arr.length > 0 ? Math.max(...arr).toFixed(1) : '--';

    return {
        temp: avg(temps),
        maxTemp: max(temps),
        minTemp: min(temps),
        humidity: avg(humidities),
        windSpeed: avg(windSpeeds),
        icon: 'https://example.com/cloudy.png', // Exemple, remplacez par des données réelles si disponibles
        condition: 'Nuageux', // Exemple, remplacez par des données réelles si disponibles
        rainChance: 20, // Exemple, remplacez par des données réelles si disponibles
    };
}

// Mettre à jour les informations météo affichées
function updateWeatherData(regionName, weatherData, stations) {
    document.getElementById('region-name').textContent = regionName;
    const now = new Date();
    document.getElementById('current-time').textContent = `À ${now.getHours()}h${String(now.getMinutes()).padStart(2, '0')}`;

    document.getElementById('temperature').textContent = `${weatherData.temp}°`;
    document.getElementById('weather-icon').src = weatherData.icon;
    document.getElementById('weather-icon').alt = weatherData.condition;
    document.getElementById('weather-condition').textContent = weatherData.condition;
    document.getElementById('rain-chance').textContent = `${weatherData.rainChance}% de chance de pluie`;
    document.getElementById('weather-stats').innerHTML = `
        <li><span class="icon">🌡</span> Max/Min: ${weatherData.maxTemp}°/${weatherData.minTemp}°</li>
        <li><span class="icon">💧</span> Humidité: ${weatherData.humidity}%</li>
        <li><span class="icon">🌬</span> Vent: ${weatherData.windSpeed} Km/h</li>
    `;

    const stationsListElement = document.getElementById('stations-list');
    if (stations.length > 0) {
        // Utiliser un `Set` pour garder une liste unique des stations
        const uniqueStations = [];
        const stationNames = new Set();

        stations.forEach(station => {
            if (!stationNames.has(station.ville)) {
                stationNames.add(station.ville);
                uniqueStations.push(station);
            }
        });

        // Afficher les stations uniques
        stationsListElement.innerHTML = '';
        uniqueStations.forEach(station => {
            const stationItem = document.createElement('li');
            stationItem.textContent = `${station.ville} - Altitude: ${station.altitude}m`;
            stationsListElement.appendChild(stationItem);
        });
    } else {
        stationsListElement.innerHTML = '<li>Aucune station disponible</li>';
    }
}

// Gestion de la recherche via la barre de recherche
document.getElementById('searchRegionButton').addEventListener('click', () => {
    const regionInput = document.getElementById('regionInput').value.trim();
    if (!regionInput) {
        alert('Veuillez entrer une région ou une station.');
        return;
    }
    showRegionData(regionInput);
});

</script>