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
let geojsonData;
let stationsData;
const stationMarkers = []; // Liste des marqueurs de stations

// Charger les données des régions et stations
Promise.all([
    fetch('/Assets/gjson/regions.geojson').then(res => res.json()), // Chemin du fichier GeoJSON
    fetch('/Web/frontController.php?action=getSynopData&controller=api').then(res => res.json()) // API SYNOP
])
    .then(([regions, stations]) => {
        geojsonData = regions;
        stationsData = stations;

        // Afficher les régions sur la carte
        L.geoJSON(geojsonData, {
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
            },
        }).addTo(map);

        // Ajouter les stations météo sur la carte
        stationsData.forEach(station => {
            const { latitude, longitude, ville, region, altitude, temp, humidity, windSpeed } = station;
            const marker = L.circleMarker([latitude, longitude], {
                radius: 8,
                color: '#004080',
                fillColor: '#0077be',
                fillOpacity: 0.7,
            }).addTo(map);

            // Ajouter un popup avec les informations de la station
            marker.bindPopup(`
                <strong>${ville}</strong><br>
                Région: ${region}<br>
                Température: ${temp || '--'}°C<br>
                Humidité: ${humidity || '--'}%<br>
                Vent: ${windSpeed || '--'} km/h<br>
                Altitude: ${altitude || 'Non spécifiée'}m
            `);

            stationMarkers.push({ marker, ville, region, altitude, temp, humidity, windSpeed });
        });
    })
    .catch(error => {
        console.error('Erreur lors du chargement des données:', error);
        alert('Erreur lors du chargement des données de la carte.');
    });

// Calculer les statistiques des stations d'une région
function calculateRegionStats(stations) {
    const temps = stations.map(station => station.temp).filter(temp => temp !== null && temp !== '--');
    const humidities = stations.map(station => station.humidity).filter(humidity => humidity !== null && humidity !== '--');
    const windSpeeds = stations.map(station => station.windSpeed).filter(wind => wind !== null && wind !== '--');

    const getStats = (values) => ({
        min: Math.min(...values),
        max: Math.max(...values),
        avg: (values.reduce((sum, val) => sum + val, 0) / values.length).toFixed(2),
    });

    return {
        temp: getStats(temps),
        humidity: getStats(humidities),
        windSpeed: getStats(windSpeeds),
    };
}

// Mettre à jour les informations météo affichées dans la section
function updateWeatherData(regionName, weatherData, stations) {
    document.getElementById('region-name').textContent = regionName;
    const now = new Date();
    document.getElementById('current-time').textContent = `À ${now.getHours()}h${String(now.getMinutes()).padStart(2, '0')}`;

    document.getElementById('temperature').textContent = `${weatherData.temp.avg || '--'}°`;
    document.getElementById('weather-icon').src = weatherData.icon || '#';
    document.getElementById('weather-icon').alt = weatherData.condition || 'Conditions météo';
    document.getElementById('weather-condition').textContent = weatherData.condition || '--';
    document.getElementById('rain-chance').textContent = `${weatherData.rainChance || '--'}% de chance de pluie`;
    document.getElementById('weather-stats').innerHTML = `
        <li><span class="icon">🌡</span> Max/Min: ${weatherData.temp.max || '--'}°/${weatherData.temp.min || '--'}°</li>
        <li><span class="icon">💧</span> Humidité Moyenne: ${weatherData.humidity.avg || '--'}%</li>
        <li><span class="icon">🌬</span> Vent Moyen: ${weatherData.windSpeed.avg || '--'} Km/h</li>
    `;

    const stationsListElement = document.getElementById('stations-list');
    if (stations.length > 0) {
        stationsListElement.innerHTML = '';
        stations.forEach(station => {
            const stationItem = document.createElement('li');
            stationItem.textContent = `${station.ville || 'Inconnue'} - Altitude: ${station.altitude || 'Non spécifiée'}m`;
            stationsListElement.appendChild(stationItem);
        });
    } else {
        stationsListElement.innerHTML = '<li>Aucune station disponible</li>';
    }
}

// Gérer la recherche de région ou de station
document.getElementById('searchRegionButton').addEventListener('click', () => {
    const regionInput = document.getElementById('regionInput').value.trim().toLowerCase();

    if (!regionInput) {
        alert('Veuillez entrer le nom d\'une région ou d\'une station.');
        return;
    }

    let found = false;

    // Recherche dans les régions
    const matchingRegion = geojsonData.features.find(
        (feature) => feature.properties.nom.toLowerCase() === regionInput
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

        const stationsInRegion = stationsData.filter(
            (station) => station.region.toLowerCase() === regionInput
        );

        const regionStats = calculateRegionStats(stationsInRegion);

        updateWeatherData(matchingRegion.properties.nom, {
            temp: regionStats.temp,
            humidity: regionStats.humidity,
            windSpeed: regionStats.windSpeed,
        }, stationsInRegion);

        found = true;
    }

    // Recherche dans les stations
    const matchingStation = stationMarkers.find(
        ({ ville }) => ville.toLowerCase() === regionInput
    );

    if (matchingStation) {
        matchingStation.marker.setStyle({
            color: 'red',
            fillColor: 'red',
            fillOpacity: 1,
        });

        map.setView(matchingStation.marker.getLatLng(), 7);
        matchingStation.marker.openPopup();

        updateWeatherData(matchingStation.ville, {
            temp: { avg: matchingStation.temp || '--', min: '--', max: '--' },
            humidity: { avg: matchingStation.humidity || '--' },
            windSpeed: { avg: matchingStation.windSpeed || '--' },
        }, [matchingStation]);

        found = true;
    }

    if (!found) {
        alert('Aucune région ou station trouvée. Veuillez vérifier le nom.');
    }
});
</script>