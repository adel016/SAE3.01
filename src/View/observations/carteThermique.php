<h1>Carte Thermique de la France</h1>
    <main class="conteneur">
        <div class="map-and-info">
            <div class="map-section">
                <div class="date-picker">
                    <label for="dateStart">Date de début :</label>
                    <input type="date" id="dateStart">
                    <label for="dateEnd">Date de fin :</label>
                    <input type="date" id="dateEnd">
                    <button id="updateMap" class="bton">Mettre à jour la carte</button>
                </div>
                <div id="mapContainer" class="map"></div>
            </div>
            <div class="info-section">
                <div class="region-search-bar">
                    <label for="regionSearch"></label>
                    <input type="text" id="regionSearch" placeholder="Entrez le nom de la région">
                    <div id="autocomplete-results"></div>
                    <button id="searchRegion" class="bton">Rechercher</button>
                    <button id="resetView" class="bton bton-reset">Réinitialiser la vue</button>
                </div>
                <div id="temperatureDetails" class="info">
                    <h2>Informations sur les températures</h2>
                    <ul id="temperatureList">
                        <!-- Les informations de température seront insérées ici -->
                    </ul>
                </div>
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

            const saveUserRequest = (regionName, avgTempDetails) => {
                fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=saveRequestThermique&controller=meteotheque', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        region: regionName,
                        details: avgTempDetails // Inclure les détails des températures moyennes
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(`Requête enregistrée avec succès pour la région : ${regionName}`);
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
                        const avgTempDetails = {}; // Détails des températures par région pour l'enregistrement

                        Object.keys(regionTemps).forEach(regionName => {
                            const temps = regionTemps[regionName];
                            const sum = temps.reduce((a, b) => a + b, 0);
                            const avg = sum / temps.length;
                            avgTemps[regionName] = avg;

                            // Construire les détails des températures
                            avgTempDetails[regionName] = `Température Moyenne: ${avg.toFixed(1)}°C`;
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

                                    // Enregistrer la requête avec les détails des températures moyennes
                                    const details = avgTempDetails[regionName] || 'Aucune donnée disponible';
                                    saveUserRequest(regionName, details);
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
                    
                    // Enregistrer la requête immédiatement après la recherche
                    const details = regionTemps !== 'Aucune donnée' ? `Température Moyenne: ${regionTemps.toFixed(1)}°C` : 'Aucune donnée disponible';
                    saveUserRequest(region.properties.nom, details);
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

<style>
    .conteneur {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .map-and-info {
        display: flex;
        width: 100%;
    }

    .map-section, .info-section {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .date-picker {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    padding: 20px;
    background-color: #f9f9f9;
    border-bottom: 1px solid #ccc;
    width: 92%; /* Augmentez la largeur */
    margin: 0 auto; /* Centre la barre */
}

.date-picker label {
    font-weight: bold;
    margin-bottom: 5px;
}

.date-picker input {
    width: 40%;
    padding: 5px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.date-picker button {
    align-self: flex-start;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px; /* Espace entre les inputs et le bouton */
}

.date-picker button:hover {
    background-color: #0056b3;
}

    .region-search-bar {
        padding: 20px;
        background-color: #f9f9f9;
        border-bottom: 1px solid #ccc;
        width: 92%; /* Augmentez la largeur */
        margin: 0 auto; /* Centre la barre */
    }

    .map {
        flex: 1;
        height: 500px;
        min-height: 500px;
    }

    .info {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
    }


.search-container {
    display: flex;
    align-items: center;
}

.search-bar {
    flex-grow: 1;
}

.btn-update, .btn-reset {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-left: 200px; /* Adjust the margin to separate it from the search bar */
}

.btn-update:hover, .btn-reset:hover {
    background-color: #0056b3;
}


</style>