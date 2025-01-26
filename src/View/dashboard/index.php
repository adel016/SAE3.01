<div class="data-container">
    <!-- Section du formulaire à gauche -->
    <div class="form-section compact-form">
        <label for="libgeo">Ville :</label>
        <input type="text" id="libgeo" placeholder="Ex : ORLY">

        <label for="nom_dept">Département :</label>
        <input type="text" id="nom_dept" placeholder="Ex : Essonne">

        <label for="region">Région :</label>
        <input type="text" id="region" placeholder="Ex : Île-de-France">

        <label for="codegeo">Code Postal :</label>
        <input type="text" id="codegeo" placeholder="Ex : 91">

        <label for="date_debut">Date Début :</label>
        <input type="date" id="date_debut">

        <label for="date_fin">Date Fin :</label>
        <input type="date" id="date_fin">

        <label for="granulariteSelect">Granularité :</label>
        <select id="granulariteSelect">
            <option value="year">Année</option>
            <option value="month">Mois</option>
            <option value="week">Semaine</option>
            <option value="day">Jour</option>
        </select>

        <div class="checkbox-group">
            <label><input type="checkbox" id="tempCheckbox" checked> Température</label>
            <label><input type="checkbox" id="humidityCheckbox"> Humidité</label>
            <label><input type="checkbox" id="windSpeedCheckbox"> Vent</label>
        </div>

        <button class="fetch-button" onclick="fetchData()">Rechercher</button>
    </div>

    <!-- Section du graphique à droite -->
    <div class="chart-container">
        <canvas id="weatherChart" width="800" height="400"></canvas>
    </div>
</div>

<!-- Tableau des résultats -->
<div class="result-container">
    <table id="result-table" style="display: none;">
        <thead>
            <tr>
                <th>Date</th>
                <th>Température (°C)</th>
                <th>Humidité (%)</th>
                <th>Vitesse du vent (km/h)</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Inclure Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Script principal -->
<script>
    let chart = null; // Variable pour stocker le graphique

    function displayChart(labels, tempData, humidityData, windSpeedData) {
        const ctx = document.getElementById('weatherChart').getContext('2d');

        // Si un graphique existe déjà, le détruire avant de créer un nouveau
        if (chart) {
            chart.destroy();
        }

        // Préparer les datasets
        const datasets = [];

        if (document.getElementById('tempCheckbox').checked) {
            datasets.push({
                label: 'Température (°C)',
                data: tempData,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.4,
            });
        }

        if (document.getElementById('humidityCheckbox').checked) {
            datasets.push({
                label: 'Humidité (%)',
                data: humidityData,
                fill: false,
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2,
                tension: 0.4,
            });
        }

        if (document.getElementById('windSpeedCheckbox').checked) {
            datasets.push({
                label: 'Vitesse du vent (km/h)',
                data: windSpeedData,
                fill: false,
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                tension: 0.4,
            });
        }

        // Créer un nouveau graphique
        chart = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Date', font: { size: 14 } } },
                    y: { title: { display: true, text: 'Valeur', font: { size: 14 } } },
                },
            },
        });
    }

    function fetchData() {
        const libgeo = document.getElementById('libgeo').value;
        const region = document.getElementById('region').value;
        const nom_dept = document.getElementById('nom_dept').value;
        const codegeo = document.getElementById('codegeo').value;
        const date_debut = document.getElementById('date_debut').value;
        const date_fin = document.getElementById('date_fin').value;
        const granularite = document.getElementById('granulariteSelect').value;

        const baseUrl = `<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php`;
        const url = `${baseUrl}?action=apiRechercheAvancee&controller=tableauDeBord&libgeo=${encodeURIComponent(
            libgeo
        )}&region=${encodeURIComponent(region)}&nom_dept=${encodeURIComponent(
            nom_dept
        )}&codegeo=${encodeURIComponent(codegeo)}&date_debut=${date_debut}&date_fin=${date_fin}&granularite=${granularite}`;

        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                if (data.results && data.results.length > 0) {
                    const labels = data.results.map((record) => record.date);
                    const tempData = data.results.map((record) => record.tc || null);
                    const humidityData = data.results.map((record) => record.u || null);
                    const windSpeedData = data.results.map((record) => record.ff || null);

                    displayChart(labels, tempData, humidityData, windSpeedData);

                    // Sauvegarder dans la Météothèque
                    saveToMeteotheque({ libgeo, region, nom_dept, date_debut, date_fin });
                } else {
                    alert('Aucun résultat trouvé.');
                }
            })
            .catch((error) => {
                console.error('Erreur lors de la récupération des données :', error);
                alert('Une erreur est survenue lors de la récupération des données.');
            });
    }

    function saveToMeteotheque(data) {
        fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=saveRequestTableaudeBordGraphique&controller=meteotheque', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
            .then((response) => response.json())
            .then((result) => {
                console.log(result.message); // Afficher le message de succès ou d'erreur
            })
            .catch((error) => {
                console.error('Erreur lors de la sauvegarde dans la Météothèque :', error);
            });
    }

</script>
