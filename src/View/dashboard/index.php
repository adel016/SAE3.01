<h1>Recherche graphique</h1>
<div class="data-container">
    <!-- Formulaire à gauche -->
    <div class="form-section">
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

        <button onclick="fetchData()">Rechercher</button>
    </div>

    <!-- Section graphique et tableau à droite -->
    <div class="result-section">
        <div id="weatherChartContainer">
            <canvas id="weatherChart"></canvas>
        </div>

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
</div>


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

        // Vérifier si la température est sélectionnée
        if (document.getElementById('tempCheckbox').checked) {
            datasets.push({
                label: 'Température (°C)', // Titre de la série
                data: tempData, // Données de la température
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.4
            });
        }

        // Vérifier si l'humidité est sélectionnée
        if (document.getElementById('humidityCheckbox').checked) {
            datasets.push({
                label: 'Humidité (%)', // Titre de la série
                data: humidityData, // Données de l'humidité
                fill: false,
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(153, 102, 255, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(153, 102, 255, 1)',
                tension: 0.4
            });
        }

        // Vérifier si la vitesse du vent est sélectionnée
        if (document.getElementById('windSpeedCheckbox').checked) {
            datasets.push({
                label: 'Vitesse du vent (km/h)', // Titre de la série
                data: windSpeedData, // Données de la vitesse du vent
                fill: false,
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(255, 99, 132, 1)',
                tension: 0.4
            });
        }

        // Créer un nouveau graphique
        chart = new Chart(ctx, {
            type: 'line', // Type de graphique (ici, une courbe)
            data: {
                labels: labels, // Labels (dates, etc.)
                datasets: datasets // Séries de données (température, humidité et/ou vitesse du vent)
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Valeur',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 12
                        }
                    }
                },
                elements: {
                    line: {
                        borderWidth: 2
                    },
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    }
                }
            }
        });
    }

    // Fonction pour récupérer et afficher les résultats
    function fetchData() {
        const libgeo = document.getElementById('libgeo').value;
        const region = document.getElementById('region').value;
        const nom_dept = document.getElementById('nom_dept').value;
        const codegeo = document.getElementById('codegeo').value;
        const date_debut = document.getElementById('date_debut').value;
        const date_fin = document.getElementById('date_fin').value;

        // Récupérer le choix de la granularité
        const granularite = document.getElementById('granulariteSelect').value;

        let adjustedDateDebut = date_debut;
        let adjustedDateFin = date_fin;

        if (granularite === 'month' && date_debut) {
            // Si la granularité est mensuelle, ajuster date_fin au dernier jour du mois
            const debutDate = new Date(date_debut);
            adjustedDateFin = new Date(debutDate.getFullYear(), debutDate.getMonth() + 1, 0).toISOString().split('T')[0];
        } else if (granularite === 'week' && date_debut) {
            // Si la granularité est hebdomadaire, ajuster date_fin à 7 jours après date_debut
            const debutDate = new Date(date_debut);
            adjustedDateFin = new Date(debutDate.getTime() + 6 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
        } else if (granularite === 'day' && date_debut) {
            // Si la granularité est journalière, ajuster la date de fin pour couvrir une seule journée
            adjustedDateFin = new Date(new Date(date_debut).getTime() + 24 * 60 * 60 * 1000 - 1).toISOString().split('T')[0];
        }

        // Construire l'URL avec la date ajustée si nécessaire
        const url = `<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/recherche_avancee.php?libgeo=${encodeURIComponent(libgeo)}&region=${encodeURIComponent(region)}&nom_dept=${encodeURIComponent(nom_dept)}&codegeo=${encodeURIComponent(codegeo)}&date_debut=${adjustedDateDebut}&date_fin=${adjustedDateFin}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                displayResults(data, granularite); // Passer la granularité au moment de l'affichage
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert("Erreur lors de la récupération des données.");
            });
    }

    function displayResults(data, granularite) {
        const table = document.getElementById('result-table');
        const tbody = table.querySelector('tbody');
        tbody.innerHTML = ''; // Efface les résultats précédents

        if (data && data.results && data.results.length > 0) {
            table.style.display = 'table'; // Affiche le tableau
            let results = data.results;

            // Trier les résultats par date en ordre croissant
            results.sort((a, b) => new Date(a.date) - new Date(b.date));

            if (granularite === 'year') {
                const monthlyData = {};

                // Grouper les données par mois
                results.forEach(record => {
                    const date = new Date(record.date);
                    const month = date.getFullYear() + '-' + (date.getMonth() + 1).toString().padStart(2, '0'); // Format YYYY-MM
                    if (!monthlyData[month]) {
                        monthlyData[month] = { temp: [], humidity: [], windSpeed: [] };
                    }
                    monthlyData[month].temp.push(record.tc); // Température
                    monthlyData[month].humidity.push(record.u); // Humidité
                    monthlyData[month].windSpeed.push(record.ff); // Vitesse du vent
                });

                // Calculer les moyennes mensuelles et afficher les données
                Object.keys(monthlyData).sort().forEach(month => {
                    const row = document.createElement('tr');
                    const avgTemp = Math.round(monthlyData[month].temp.reduce((a, b) => a + b, 0) / monthlyData[month].temp.length);
                    const avgHumidity = (monthlyData[month].humidity.reduce((a, b) => a + b, 0) / monthlyData[month].humidity.length).toFixed(2);
                    const avgWindSpeed = (monthlyData[month].windSpeed.reduce((a, b) => a + b, 0) / monthlyData[month].windSpeed.length).toFixed(2);

                    row.innerHTML = `
                        <td>${month}</td>
                        <td>${avgTemp}</td>
                        <td>${avgHumidity}</td>
                        <td>${avgWindSpeed}</td>
                    `;
                    tbody.appendChild(row);
                });

                // Préparer les données pour le graphique
                const labels = Object.keys(monthlyData).sort();
                const tempData = labels.map(month => Math.round(monthlyData[month].temp.reduce((a, b) => a + b, 0) / monthlyData[month].temp.length));
                const humidityData = labels.map(month => (monthlyData[month].humidity.reduce((a, b) => a + b, 0) / monthlyData[month].humidity.length).toFixed(2));
                const windSpeedData = labels.map(month => (monthlyData[month].windSpeed.reduce((a, b) => a + b, 0) / monthlyData[month].windSpeed.length).toFixed(2));

                displayChart(labels, tempData, humidityData, windSpeedData);
            } else if (granularite === 'month' || granularite === 'week') {
                const dailyData = {};

                results.forEach(record => {
                    const date = record.date.split('T')[0];
                    if (!dailyData[date]) {
                        dailyData[date] = { temp: [], humidity: [], windSpeed: [] };
                    }
                    dailyData[date].temp.push(record.tc); // Température
                    dailyData[date].humidity.push(record.u); // Humidité
                    dailyData[date].windSpeed.push(record.ff); // Vitesse du vent
                });

                Object.keys(dailyData).sort().forEach(date => {
                    const row = document.createElement('tr');
                    const avgTemp = Math.round(dailyData[date].temp.reduce((a, b) => a + b, 0) / dailyData[date].temp.length);
                    const avgHumidity = (dailyData[date].humidity.reduce((a, b) => a + b, 0) / dailyData[date].humidity.length).toFixed(2);
                    const avgWindSpeed = (dailyData[date].windSpeed.reduce((a, b) => a + b, 0) / dailyData[date].windSpeed.length).toFixed(2);

                    row.innerHTML = `
                        <td>${date}</td>
                        <td>${avgTemp}</td>
                        <td>${avgHumidity}</td>
                        <td>${avgWindSpeed}</td>
                    `;
                    tbody.appendChild(row);
                });

                const labels = Object.keys(dailyData).sort();
                const tempData = labels.map(date => Math.round(dailyData[date].temp.reduce((a, b) => a + b, 0) / dailyData[date].temp.length));
                const humidityData = labels.map(date => (dailyData[date].humidity.reduce((a, b) => a + b, 0) / dailyData[date].humidity.length).toFixed(2));
                const windSpeedData = labels.map(date => (dailyData[date].windSpeed.reduce((a, b) => a + b, 0) / dailyData[date].windSpeed.length).toFixed(2));

                displayChart(labels, tempData, humidityData, windSpeedData);
            } else if (granularite === 'day') {
                const hourlyData = {};

                results.forEach(record => {
                    const time = record.date.split('T')[1].split(':')[0];
                    if (!hourlyData[time]) {
                        hourlyData[time] = { temp: [], humidity: [], windSpeed: [] };
                    }
                    hourlyData[time].temp.push(record.tc);
                    hourlyData[time].humidity.push(record.u);
                    hourlyData[time].windSpeed.push(record.ff);
                });

                Object.keys(hourlyData).sort().forEach(hour => {
                    const row = document.createElement('tr');
                    const avgTemp = Math.round(hourlyData[hour].temp.reduce((a, b) => a + b, 0) / hourlyData[hour].temp.length);
                    const avgHumidity = (hourlyData[hour].humidity.reduce((a, b) => a + b, 0) / hourlyData[hour].humidity.length).toFixed(2);
                    const avgWindSpeed = (hourlyData[hour].windSpeed.reduce((a, b) => a + b, 0) / hourlyData[hour].windSpeed.length).toFixed(2);

                    row.innerHTML = `
                        <td>${hour.padStart(2, '0')}h00</td>
                        <td>${avgTemp}</td>
                        <td>${avgHumidity}</td>
                        <td>${avgWindSpeed}</td>
                    `;
                    tbody.appendChild(row);
                });

                const labels = Object.keys(hourlyData).sort().map(hour => `${hour.padStart(2, '0')}h00`);
                const tempData = labels.map((_, i) => Math.round(hourlyData[Object.keys(hourlyData).sort()[i]].temp.reduce((a, b) => a + b, 0) / hourlyData[Object.keys(hourlyData).sort()[i]].temp.length));
                const humidityData = labels.map((_, i) => (hourlyData[Object.keys(hourlyData).sort()[i]].humidity.reduce((a, b) => a + b, 0) / hourlyData[Object.keys(hourlyData).sort()[i]].humidity.length).toFixed(2));
                const windSpeedData = labels.map((_, i) => (hourlyData[Object.keys(hourlyData).sort()[i]].windSpeed.reduce((a, b) => a + b, 0) / hourlyData[Object.keys(hourlyData).sort()[i]].windSpeed.length).toFixed(2));

                displayChart(labels, tempData, humidityData, windSpeedData);
            } else {
                results.forEach(record => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${record.date}</td>
                        <td>${Math.round(record.tc)}</td> <!-- Température arrondie -->
                        <td>${record.u}</td>
                        <td>${record.ff}</td>
                    `;
                    tbody.appendChild(row);
                });

                const labels = results.map(record => record.date);
                const tempData = results.map(record => Math.round(record.tc)); // Température arrondie
                const humidityData = results.map(record => record.u);
                const windSpeedData = results.map(record => record.ff);

                displayChart(labels, tempData, humidityData, windSpeedData);
            }
        } else {
            table.style.display = 'none'; // Cache le tableau si aucune donnée
            alert('Aucun résultat trouvé.');
        }
    }
</script>

<style>
    .data-container {
        display: flex;
        justify-content: space-between;
        margin: 20px;
    }

    .form-section {
        width: 30%;
    }

    .form-section label {
        display: block;
        margin-top: 10px;
    }

    .form-section input,
    .form-section select {
        width: 100%;
        padding: 5px;
        margin-top: 5px;
    }

    .checkbox-group {
        margin-top: 10px;
    }

    .checkbox-group label {
        display: block;
    }

    .result-section {
        width: 65%;
    }

    #weatherChartContainer {
        margin-bottom: 20px;
    }

    #result-table {
        width: 100%;
        border-collapse: collapse;
    }

    #result-table th,
    #result-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    #result-table th {
        background-color: #5d9ee7;
    }

</style>