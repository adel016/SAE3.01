<h1>Carte Thermique de la France</h1>
<main class="index">
    <div class="carte">
        <div class="region-search">
            <label for="regionInput">Rechercher une région :</label>
            <input type="text" id="regionInput" placeholder="Entrez le nom de la région">
            <button id="searchRegionButton">Rechercher</button>
            <button id="resetButton">Réinitialiser la vue</button>
        </div>
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>
<script>
    const map = L.map('map').setView([46.603354, 1.888334], 6);
    let geojsonLayer;

    // Ajout des tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Fonction pour calculer la couleur thermique en fonction de la valeur
    const getHeatColor = (value) => {
        if (value === null) return 'gray'; // Pas de données
        return value > 30 ? 'red' :
               value > 20 ? 'orange' :
               value > 10 ? 'yellow' :
               value > 5 ? 'lime' : 'blue' ;
    };

    let geojsonData; // Variable globale pour stocker les données GeoJSON

    // Charger les données GeoJSON des régions
    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/gjson/regions.geojson')
        .then(response => response.json())
        .then(data => {
            geojsonData = data; // Stocker les données GeoJSON
            // Charger les données des stations et calculer les températures par région
            fetch(`<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=getHeatmapDataByRegion&controller=api`)
                .then(response => response.json())
                .then(stationsData => {
                    // Calculer la moyenne des températures pour chaque région
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

                    // Moyenne des températures pour chaque région
                    Object.keys(regionTemps).forEach(regionName => {
                        const temps = regionTemps[regionName];
                        const sum = temps.reduce((a, b) => a + b, 0);
                        const avg = sum / temps.length;
                        regionTemps[regionName] = avg;
                    });

                    // Appliquer les styles en fonction des températures moyennes
                    geojsonLayer = L.geoJSON(geojsonData, {
                        style: (feature) => {
                            const regionName = feature.properties.nom;
                            const avgTemp = regionTemps[regionName] || null;
                            return {
                                fillColor: getHeatColor(avgTemp),
                                weight: 1,
                                color: 'black',
                                fillOpacity: 0.6
                            };
                        }
                    }).addTo(map);
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des données des stations :', error);
                });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des données GeoJSON :', error);
        });

    // Fonction pour rechercher une région et zoomer dessus
    const searchRegion = () => {
        const regionName = document.getElementById('regionInput').value.trim().toLowerCase();

        if (!regionName) {
            alert('Veuillez entrer un nom de région.');
            return;
        }

        // Trouver la région correspondante
        const region = geojsonData.features.find(feature =>
            feature.properties.nom.toLowerCase() === regionName
        );

        if (region) {
            // Zoom sur la région trouvée
            const bounds = L.geoJSON(region).getBounds();
            map.fitBounds(bounds);
        } else {
            alert('Aucune région correspondante trouvée.');
        }
    };

    // Réinitialisation de la vue
    const resetView = () => {
        map.setView([46.603354, 1.888334], 6);
    };

    // Écouteurs d'événements pour les boutons
    document.getElementById('searchRegionButton').addEventListener('click', searchRegion);
    document.getElementById('resetButton').addEventListener('click', resetView);
</script>
