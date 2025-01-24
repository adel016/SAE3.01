<h1>Carte Thermique de la France</h1>
<main class="index">
    <div class="carte">
        <div class="region-search">
            <label for="regionInput">Rechercher une région :</label>
            <input type="text" id="regionInput" placeholder="Entrez le nom de la région">
            <button id="searchRegionButton">Rechercher</button>
        </div>
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>
<script>
    const map = L.map('map', {
        attributionControl: false,
    }).setView([46.603354, 1.888334], 6);

    // Charger les tuiles de la carte
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Charger les contours des régions de France
    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/gjson/regions.geojson')
        .then(response => response.json())
        .then(data => {
            L.geoJSON(data, {
                style: {
                    color: '#004080',
                    weight: 1,
                    fillOpacity: 0.2
                }
            }).addTo(map);
        });

    // Fonction pour charger les données de la carte thermique
    const loadHeatmap = (regionName = null) => {
        const url = regionName
            ? `<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=getHeatmapDataByRegion&controller=api&region=${regionName}`
            : `<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=getHeatmapDataByRegion&controller=api`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const heatData = data.map(item => [item.lat, item.lon, item.value]);
                const heatLayer = L.heatLayer(heatData, { radius: 25, blur: 15, maxZoom: 17 }).addTo(map);
            });
    };

    // Charger la carte thermique globale au démarrage
    loadHeatmap();

    // Rechercher une région
    document.getElementById('searchRegionButton').addEventListener('click', () => {
        const regionInput = document.getElementById('regionInput').value.trim();
        if (regionInput) {
            loadHeatmap(regionInput);
        }
    });
</script>
