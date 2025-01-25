<h1>Carte Thermique de la France</h1>
<main class="conteneur">
    <div class="controls">
        <div class="date-picker">
            <label for="dateStart">Date de début :</label>
            <input type="date" id="dateStart">
            <label for="dateEnd">Date de fin :</label>
            <input type="date" id="dateEnd">
            <button id="updateMap" class="bton">Mettre à jour la carte</button>
        </div>

        <div class="region-search-bar">
            <label for="regionSearch">Rechercher une région :</label>
            <input type="text" id="regionSearch" placeholder="Entrez le nom de la région">
            <button id="searchRegion" class="bton">Rechercher</button>
            <button id="resetView" class="bton bton-reset">Réinitialiser la vue</button>
        </div>
    </div>

    <div class="map-and-info">
        <div id="mapContainer"></div>
        <div id="temperatureDetails">
            <h2>Informations sur les températures</h2>
            <ul id="temperatureList">
                <!-- Les informations de température seront insérées ici -->
            </ul>
        </div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const map = L.map('mapContainer', {
        attributionControl: false,
    }).setView([46.603354, 1.888334], 6);
    let geojsonLayer;
    let geojsonData; // Variable pour stocker les données GeoJSON
    let avgTemps = {}; // Températures moyennes par région

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const getHeatColor = (value) => {
        if (value === null) return 'gray'; 
        return value > 30 ? 'red' :
               value > 20 ? 'orange' :
               value > 10 ? 'yellow' :
               value > 5 ? 'lime' : 'blue';
    };

    const updateTemperatureInfo = (regionTemps) => {
        const temperatureList = document.getElementById('temperatureList');
        temperatureList.innerHTML = ''; // Réinitialiser la liste

        if (Object.keys(regionTemps).length === 0) {
            temperatureList.innerHTML = '<li>Aucune donnée disponible pour la plage de dates sélectionnée.</li>';
            return;
        }

        Object.entries(regionTemps).forEach(([regionName, avgTemp]) => {
            const li = document.createElement('li');
            li.innerHTML = `
                <strong>Région :</strong> ${regionName}<br>
                <strong>Température moyenne :</strong> ${avgTemp.toFixed(1)}°C
            `;
            temperatureList.appendChild(li);
        });
    };

    const saveUserRequest = (regionName) => {
        fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=saveRequestThermique&controller=meteotheque', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ region: regionName })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`Recherche sur la carte thermique sur la région : ${regionName}`);
                } else {
                    console.log('Utilisateur non connecté ou échec de l\'enregistrement.');
                }
            })
            .catch(error => console.error('Erreur lors de l\'enregistrement de la requête :', error));
    };

    const loadHeatmapData = (startDate, endDate) => {
        if (!geojsonData) {
            console.error('Les données GeoJSON ne sont pas chargées.');
            return;
        }

        const url = new URL('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php');
        url.searchParams.append('action', 'getHeatmapDataByRegion');
        url.searchParams.append('controller', 'api');
        if (startDate) url.searchParams.append('startDate', startDate);
        if (endDate) url.searchParams.append('endDate', endDate);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur serveur : ${response.statusText}`);
                }
                return response.json();
            })
            .then(stationsData => {
                const regionTemps = {};

                stationsData.forEach(station => {
                    const { lat, lon, value } = station;
                    const region = geojsonData.features.find(feature =>
                        L.geoJSON(feature).getBounds().contains([lat, lon])
                    );

                    if (region) {
                        const regionName = region.properties.nom;
                        if (!regionTemps[regionName]) {
                            regionTemps[regionName] = [];
                        }
                        regionTemps[regionName].push(value);
                    }
                });

                avgTemps = {}; // Réinitialiser les températures moyennes
                Object.keys(regionTemps).forEach(regionName => {
                    const temps = regionTemps[regionName];
                    const sum = temps.reduce((a, b) => a + b, 0);
                    const avg = sum / temps.length;
                    avgTemps[regionName] = avg;
                });

                updateTemperatureInfo(avgTemps);

                if (geojsonLayer) map.removeLayer(geojsonLayer);

                geojsonLayer = L.geoJSON(geojsonData, {
                    style: (feature) => {
                        const regionName = feature.properties.nom;
                        const avgTemp = avgTemps[regionName] || null;
                        return {
                            fillColor: getHeatColor(avgTemp),
                            weight: 1,
                            color: 'black',
                            fillOpacity: 0.6
                        };
                    },
                    onEachFeature: (feature, layer) => {
                        layer.on('click', () => {
                            const regionName = feature.properties.nom;
                            const avgTemp = avgTemps[regionName] || 'Aucune donnée';
                            const temperatureList = document.getElementById('temperatureList');
                            temperatureList.innerHTML = `
                                <li>
                                    <strong>Région :</strong> ${regionName}<br>
                                    <strong>Température moyenne :</strong> ${avgTemp !== 'Aucune donnée' ? `${avgTemp.toFixed(1)}°C` : avgTemp}
                                </li>
                            `;
                            saveUserRequest(regionName); // Enregistrer la requête
                        });
                    }
                }).addTo(map);
            })
            .catch(error => console.error('Erreur lors du chargement des données des stations :', error));
    };

    const resetMapView = () => {
        map.setView([46.603354, 1.888334], 6);
        if (geojsonLayer) {
            map.removeLayer(geojsonLayer);
        }
        loadHeatmapData(null, null);
    };

    const searchRegion = () => {
        const regionName = document.getElementById('regionSearch').value.trim().toLowerCase();

        if (!regionName) {
            alert('Veuillez entrer un nom de région.');
            return;
        }

        const region = geojsonData.features.find(feature =>
            feature.properties.nom.toLowerCase() === regionName
        );

        if (region) {
            const bounds = L.geoJSON(region).getBounds();
            map.fitBounds(bounds);

            const regionTemps = avgTemps[region.properties.nom] || 'Aucune donnée';
            const temperatureList = document.getElementById('temperatureList');
            temperatureList.innerHTML = `
                <li>
                    <strong>Région :</strong> ${region.properties.nom}<br>
                    <strong>Température moyenne :</strong> ${regionTemps !== 'Aucune donnée' ? `${regionTemps.toFixed(1)}°C` : regionTemps}
                </li>
            `;
            saveUserRequest(region.properties.nom); // Enregistrer la requête
        } else {
            alert('Aucune région correspondante trouvée.');
        }
    };

    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/gjson/regions.geojson')
        .then(response => response.json())
        .then(data => {
            geojsonData = data; // Stocker les données GeoJSON
            loadHeatmapData(null, null); // Charger les données initiales
        })
        .catch(error => console.error('Erreur lors du chargement des données GeoJSON :', error));

    document.getElementById('updateMap').addEventListener('click', () => {
        const startDate = document.getElementById('dateStart').value;
        const endDate = document.getElementById('dateEnd').value;

        if (!startDate || !endDate) {
            alert('Veuillez sélectionner une plage de dates.');
            return;
        }

        loadHeatmapData(startDate, endDate);
    });

    document.getElementById('resetView').addEventListener('click', resetMapView);
    document.getElementById('searchRegion').addEventListener('click', searchRegion);
});
</script>