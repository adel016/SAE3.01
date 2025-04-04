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
                <ul id="weather-stats">
                    <li><span class="icon">🌡</span> Max/Min: --°/--°</li>
                    <li><span class="icon">💧</span> Humidité: --%</li>
                    <li><span class="icon">🌬</span> Vent: -- Km/h</li>
                </ul>
            </div>
            <hr>
            <div class="stations-list">
                <h3>Stations dans la région :</h3>
                <ul id="stations-list">
                    <li>Aucune station disponible</li>
                </ul>
            </div>
        </div>

        <!-- Conteneur carte-->
        <div class="carte">
            <!-- Barre de recherche -->
            <div class="region-search-bar">
                    <label for="regionSearch"></label>
                <input type="text" id="regionSearch" placeholder="Entrez le nom de la région">
                <button id="searchRegion" class="bton">Rechercher</button>
                <button id="resetView" class="bton bton-reset">Réinitialiser la vue</button>
            </div>

            <br>
            <!-- Carte interactive -->
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
    </section>
</main>

<script>
// INITIALISATION DE LA CARTE
const map = L.map('map', {
    attributionControl: false,
}).setView([46.603354, 1.888334], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
}).addTo(map);

let geojsonData;
let stationsData;
let geojsonLayer;
const stationMarkers = [];

const defaultRegionName = "Île-de-France";

// Charger les données GeoJSON et station
Promise.all([
    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/gjson/regions.geojson').then(res => res.json()),
    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=getSynopData&controller=api').then(res => res.json())
])
    .then(([regions, stations]) => {
        geojsonData = regions;
        stationsData = stations;

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
                        color: 'yellow',
                        weight: 2,
                        fillColor:'gold',
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
                layer.on('click', () => {
                    showRegionData(feature.properties.nom);
                });
                layer.bindPopup(`Région : ${feature.properties.nom}`);
            }
        }).addTo(map);

        showRegionData(defaultRegionName, false);
        stationsData.forEach(station => addStationMarker(station));
    })
    .catch(error => {
        console.error('Erreur lors du chargement des données:', error);
        alert('Erreur lors du chargement des données de la carte.');
    });

function addStationMarker(station) {
    const { latitude, longitude, ville, temp, humidity, windSpeed } = station;

    if (latitude && longitude) {
        const marker = L.circleMarker([latitude, longitude], {
            radius: 8,
            color: '#004080',
            fillColor: '#0077be',
            fillOpacity: 0.7,
        }).addTo(map);

        marker.bindPopup(`
            <strong>${ville}</strong><br>
            Température: ${temp}°C<br>
            Humidité: ${humidity}%<br>
            Vent: ${windSpeed} km/h
        `);

        marker.on('click', () => {
            if (ville && temp !== undefined && humidity !== undefined && windSpeed !== undefined) {
                updateWeatherData(ville, {
                    temp,
                    humidity,
                    windSpeed,
                    icon: getWeatherIcon(temp),
                    condition: getWeatherCondition(temp),
                }, true);

                // Enregistrer la station sélectionnée
                saveStationRequest(ville, temp, humidity, windSpeed);
            } else {
                console.error("Données station invalides :", station);
            }
        });

        stationMarkers.push(marker);
    }
}

function showRegionData(regionName, saveRequest = true) {
    stationMarkers.forEach(marker => map.removeLayer(marker));
    stationMarkers.length = 0;

    const stationsInRegion = stationsData.filter(
        station => station.region.toLowerCase() === regionName.toLowerCase()
    );

    const uniqueStations = [];
    const stationNames = new Set();

    stationsInRegion.forEach(station => {
        if (!stationNames.has(station.ville)) {
            stationNames.add(station.ville);
            uniqueStations.push(station);
        }
    });

    const weatherData = calculateWeatherData(uniqueStations);
    updateWeatherData(regionName, weatherData);

    const stationsListElement = document.getElementById('stations-list');
    stationsListElement.innerHTML = '';

    uniqueStations.forEach(station => {
        const stationItem = document.createElement('li');
        stationItem.textContent = `${station.ville} - Température: ${station.temp}°C`;
        stationsListElement.appendChild(stationItem);
        addStationMarker(station);
    });

    const matchingRegion = geojsonData.features.find(
        feature => feature.properties.nom.toLowerCase() === regionName.toLowerCase()
    );
    if (matchingRegion) {
        const bounds = L.geoJSON(matchingRegion).getBounds();
        map.fitBounds(bounds);
    }

    const details = uniqueStations
        .map(station => `${station.ville}: Temp ${station.temp}°C, Humidité ${station.humidity}%`)
        .join("; ");

    // Ne pas enregistrer la région par défaut (IDF) au premier chargement
    if (saveRequest) {
        saveRegionRequest(regionName, details);
    }
}

function saveRegionRequest(regionName, details) {
    const url = '<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=saveRequest&controller=meteotheque';

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            region: regionName,
            details: details // Inclure les détails des stations
        })
    })
    .then(response => {
        // Vérifiez si la réponse est au format JSON
        if (!response.ok) {
            return response.text().then(text => {
                console.error(`Erreur HTTP : ${response.status}\n${text}`);
                throw new Error(`Erreur HTTP : ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Requête enregistrée avec succès.');
        } else {
            console.warn('Erreur lors de l\'enregistrement :', data.message);
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'enregistrement de la requête :', error);
    });

    console.log("Détails de la région :", details);
}

function saveStationRequest(stationId, temp, humidity, windSpeed) {
    const url = '<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=saveStationRequest&controller=meteotheque';

    const details = `Température: ${temp !== null && temp !== undefined ? temp : 'N/A'}°C, Humidité: ${humidity !== null && humidity !== undefined ? humidity : 'N/A'}%, Vent: ${windSpeed !== null && windSpeed !== undefined ? windSpeed : 'N/A'} km/h`;

    console.log("Station ID :", stationId);
    console.log("Température :", temp);
    console.log("Humidité :", humidity);
    console.log("Vent :", windSpeed);

    const requestData = {
        station_id: stationId,
        details: details // Envoyer les détails
    };

    console.log("Données envoyées pour la station :", requestData);

    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erreur HTTP : ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Station enregistrée avec succès.');
        } else {
            console.warn('Erreur lors de l\'enregistrement :', data.message);
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function calculateWeatherData(stations) {
    console.log("Stations reçues :", stations);

    const temps = stations.map(station => parseFloat(station.temp)).filter(temp => !isNaN(temp));
    const humidities = stations.map(station => parseFloat(station.humidity)).filter(humidity => !isNaN(humidity)); // Ligne concernée
    const windSpeeds = stations.map(station => parseFloat(station.windSpeed)).filter(windSpeed => !isNaN(windSpeed));

    const avg = arr => arr.length > 0 ? (arr.reduce((a, b) => a + b, 0) / arr.length).toFixed(1) : '--';
    const min = arr => arr.length > 0 ? Math.min(...arr).toFixed(1) : '--';
    const max = arr => arr.length > 0 ? Math.max(...arr).toFixed(1) : '--';

    return {
        temp: avg(temps),
        maxTemp: max(temps),
        minTemp: min(temps),
        humidity: avg(humidities),
        windSpeed: avg(windSpeeds),
        icon: getWeatherIcon(avg(temps)),
        condition: getWeatherCondition(avg(temps)),
    };
}

function updateWeatherData(name, weatherData, isStation = false) {
    document.getElementById('region-name').textContent = name;
    const now = new Date();
    document.getElementById('current-time').textContent = `À ${now.getHours()}h${String(now.getMinutes()).padStart(2, '0')}`;
    document.getElementById('temperature').textContent = `${weatherData.temp}°`;
    document.getElementById('weather-icon').src = weatherData.icon;
    document.getElementById('weather-icon').alt = weatherData.condition;
    document.getElementById('weather-condition').textContent = weatherData.condition;
    
    let statsHTML = `
        <li><span class="icon">💧</span> Humidité: ${weatherData.humidity}%</li>
        <li><span class="icon">🌬</span> Vent: ${weatherData.windSpeed} Km/h</li>
    `;
    
    if (!isStation) {
        statsHTML = `<li><span class="icon">🌡</span> Max/Min: ${weatherData.maxTemp}°/${weatherData.minTemp}°</li>` + statsHTML;
    }
    
    document.getElementById('weather-stats').innerHTML = statsHTML;
}

document.getElementById('searchRegion').addEventListener('click', () => {
    const inputElem = document.getElementById('regionSearch');
    const searchInput = inputElem ? inputElem.value.trim().toLowerCase() : '';
    if (!searchInput) {
        alert('Veuillez entrer une région ou une station.');
        return;
    }

    const matchingStation = stationsData.find(station => station.ville.toLowerCase() === searchInput);
    if (matchingStation) {
        const { latitude, longitude, ville, temp, humidity, windSpeed } = matchingStation;
        if (latitude !== null && longitude !== null) {
            map.setView([latitude, longitude], 10);
            showRegionData(matchingStation.region);

            // Ajouter la station recherchée à la météothèque
            saveStationRequest(ville, temp, humidity, windSpeed);
        } else {
            alert('Les coordonnées de cette station ne sont pas disponibles.');
        }
        return;
    }

    const matchingRegion = geojsonData.features.find(
        feature => feature.properties.nom.toLowerCase() === searchInput
    );

    if (matchingRegion) {
        showRegionData(matchingRegion.properties.nom);
    } else {
        alert('Aucune région ou station correspondante trouvée. Veuillez réessayer.');
    }
});

document.getElementById('resetView').addEventListener('click', () => {
    // Réinitialiser la carte à la vue par défaut
    map.setView([46.603354, 1.888334], 6);

    // Réinitialiser le champ de recherche
    document.getElementById('regionSearch').value = '';

    // Réafficher la région par défaut sans sauvegarder
    showRegionData(defaultRegionName, false);
});


function getWeatherIcon(temperature) {
    const baseUrl = 'https://openweathermap.org/img/wn/';
    let iconCode = '01d';
    if (temperature < 0) iconCode = '13d';
    else if (temperature < 10) iconCode = '09d';
    else if (temperature < 20) iconCode = '03d';
    return `${baseUrl}${iconCode}@2x.png`;
}

function getWeatherCondition(temperature) {
    if (temperature < 0) return 'Neigeux';
    if (temperature < 10) return 'Frais';
    if (temperature < 20) return 'Tempéré';
    return 'Chaud';
}

</script>
