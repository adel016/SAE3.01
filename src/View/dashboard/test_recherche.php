<h1>Recherche libre</h1>
<div class="data-container">
    <!-- Section du formulaire -->
    <div class="form-section">
        <label for="libgeo">Ville :</label>
        <input type="text" id="libgeo" placeholder="Ex : ORLY">

        <label for="region">Région :</label>
        <input type="text" id="region" placeholder="Ex : Île-de-France">

        <label for="nom_dept">Département :</label>
        <input type="text" id="nom_dept" placeholder="Ex : Essonne">

        <label for="codegeo">Code Département :</label>
        <input type="text" id="codegeo" placeholder="Ex : 91">

        <label for="date_debut">Date Début :</label>
        <input type="date" id="date_debut">

        <label for="date_fin">Date Fin :</label>
        <input type="date" id="date_fin">

        <h3>Choisissez le style d'affichage :</h3>
        <label><input type="radio" name="display-mode" value="json" checked> JSON brut</label>
        <label><input type="radio" name="display-mode" value="table"> Tableau</label>

        <button onclick="fetchData()">Rechercher</button>
    </div>

    <!-- Boutons de téléchargement et cases à cocher -->
    <div style="margin-top: 20px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
        <div class="column-toggle" style="display: none; flex-grow: 1;">
            <label><input type="checkbox" class="column-checkbox" data-column="0" checked> Région</label>
            <label><input type="checkbox" class="column-checkbox" data-column="1" checked> Département</label>
            <label><input type="checkbox" class="column-checkbox" data-column="2" checked> Ville</label>
            <label><input type="checkbox" class="column-checkbox" data-column="3" checked> Date</label>
            <label><input type="checkbox" class="column-checkbox" data-column="4" checked> Vitesse du vent</label>
            <label><input type="checkbox" class="column-checkbox" data-column="5" checked> Pression</label>
            <label><input type="checkbox" class="column-checkbox" data-column="6" checked> Humidité</label>
            <label><input type="checkbox" class="column-checkbox" data-column="7" checked> Température</label>
            <label><input type="checkbox" class="column-checkbox" data-column="8" checked> Visibilité</label>
            <label><input type="checkbox" class="column-checkbox" data-column="9" checked> Pression au niveau de la mer</label>
        </div>
        <div class="download-buttons" style="display: none;">
            <button onclick="downloadData('json')">Télécharger JSON</button>
            <button onclick="downloadData('csv')">Télécharger CSV</button>
        </div>
    </div>

    <!-- Section des résultats -->
    <div class="result-section">
        <h3>Résultats</h3>
        <div id="result-json" style="white-space: pre-wrap; display: none;"></div>
        <table id="result-table" style="display: none; margin-top: 20px;">
            <thead>
                <tr>
                    <th>Région</th>
                    <th>Département</th>
                    <th>Ville</th>
                    <th>Date</th>
                    <th>Vitesse du vent (km/h)</th>
                    <th>Pression (Pa)</th>
                    <th>Humidité (%)</th>
                    <th>Température (°C)</th>
                    <th>Visibilité (m)</th>
                    <th>Pression au niveau de la mer (Pa)</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
let fetchedData = []; // Variable globale pour stocker les données récupérées

function fetchData() {
    const libgeo = document.getElementById('libgeo').value;
    const region = document.getElementById('region').value;
    const nom_dept = document.getElementById('nom_dept').value;
    const codegeo = document.getElementById('codegeo').value;
    const date_debut = document.getElementById('date_debut').value;
    const date_fin = document.getElementById('date_fin').value;

    // Récupérer le format d'affichage (JSON ou Tableau)
    const displayMode = document.querySelector('input[name="display-mode"]:checked').value;

    const url = '<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=apiRechercheAvancee&controller=tableauDeBord&libgeo=' + 
                encodeURIComponent(libgeo) + 
                '&region=' + encodeURIComponent(region) + 
                '&nom_dept=' + encodeURIComponent(nom_dept) + 
                '&codegeo=' + encodeURIComponent(codegeo) + 
                '&date_debut=' + date_debut + 
                '&date_fin=' + date_fin;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                fetchedData = data.results; // Stocker les données pour le téléchargement

                const table = document.getElementById('result-table');
                const tbody = table.querySelector('tbody');
                tbody.innerHTML = '';

                if (displayMode === 'table') {
                    data.results.forEach(record => {
                        const fields = record;
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${fields.nom_reg || 'N/A'}</td>
                            <td>${fields.nom_dept || 'N/A'}</td>
                            <td>${fields.libgeo || 'N/A'}</td>
                            <td>${fields.date || 'N/A'}</td>
                            <td>${fields.ff || 'N/A'}</td>
                            <td>${fields.pmer || 'N/A'}</td>
                            <td>${fields.u || 'N/A'}</td>
                            <td>${fields.tc || 'N/A'}</td>
                            <td>${fields.vv || 'N/A'}</td>
                            <td>${fields.pres || 'N/A'}</td>
                        `;
                        tbody.appendChild(row);
                    });

                    table.style.display = 'table';
                    document.getElementById('result-json').style.display = 'none';
                    document.querySelector('.column-toggle').style.display = 'block';
                } else {
                    document.getElementById('result-json').textContent = JSON.stringify(data, null, 2);
                    document.getElementById('result-json').style.display = 'block';
                    table.style.display = 'none';
                    document.querySelector('.column-toggle').style.display = 'none';
                }

                document.querySelector('.download-buttons').style.display = 'block';
            } else {
                alert('Aucun résultat trouvé.');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des données :', error);
            alert('Une erreur est survenue lors de la récupération des données.');
        });
}

function downloadData(format) {
    if (fetchedData.length === 0) {
        alert('Aucune donnée à télécharger.');
        return;
    }

    // 🔹 Récupérer les critères pour nommer le fichier
    const libgeo = document.getElementById('libgeo').value.trim();
    const region = document.getElementById('region').value.trim();
    const dateDebut = document.getElementById('date_debut').value.trim();

    // 🔹 Construire le nom du fichier dynamiquement sans "inconnu"
    let fileParts = [];
    if (region) fileParts.push(region);
    if (libgeo) fileParts.push(libgeo);
    if (dateDebut) fileParts.push(dateDebut);

    const fileName = fileParts.join('_').replace(/\s+/g, '_'); // 🔹 Assemble le nom sans "inconnu"

    if (fileName === "") {
        alert("Aucun critère de recherche n'a été fourni. Impossible de nommer le fichier.");
        return;
    }

    if (format === 'json') {
        const blob = new Blob([JSON.stringify(fetchedData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${fileName}.json`; // 🔹 Nom dynamique
        a.click();
    } else if (format === 'csv') {
        const csvHeaders = ['Région', 'Département', 'Ville', 'Date', 'Vitesse du vent', 'Pression', 'Humidité', 'Température', 'Visibilité', 'Pression au niveau de la mer'];
        const csvRows = fetchedData.map(record => [
            record.nom_reg || 'N/A',
            record.nom_dept || 'N/A',
            record.libgeo || 'N/A',
            record.date || 'N/A',
            record.ff || 'N/A',
            record.pmer || 'N/A',
            record.u || 'N/A',
            record.tc || 'N/A',
            record.vv || 'N/A',
            record.pres || 'N/A'
        ]);

        const csvContent = [csvHeaders.join(','), ...csvRows.map(row => row.join(','))].join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${fileName}.csv`; // 🔹 Nom dynamique
        a.click();
    }
}


// Masquer/afficher des colonnes selon les cases cochées
document.addEventListener('change', (e) => {
    if (e.target.classList.contains('column-checkbox')) {
        const column = e.target.dataset.column;
        const table = document.getElementById('result-table');
        const cells = table.querySelectorAll(`td:nth-child(${parseInt(column) + 1}), th:nth-child(${parseInt(column) + 1})`);
        cells.forEach(cell => {
            cell.style.display = e.target.checked ? '' : 'none';
        });
    }
});
</script>

<style> /* Styles CSS pour la page */           

.data-container {
    margin-left: -50px;
    margin: 0 auto;
    padding: 20px;
}    

.form-section {
    margin-bottom: 20px;
}   

.form-section label {
    display: block;
    margin-bottom: 5px;
}   

.form-section input {
    width: 100%;
    padding: 5px;
    margin-bottom: 10px;
}

.form-section button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}

.column-toggle {
    margin-top: 20px;
    display: none;
}


.column-toggle label {
    display: block;
    margin-bottom: 5px;
}

.download-buttons {
    display: none;
}

.download-buttons button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}

.result-section {
    margin-top: 20px;
}

.result-section h3 {
    margin-right: 850px;
    text-align: left; /* Ajoutez cette ligne pour aligner le titre à gauche */
}

#result-json {
    background-color: #f8f9fa;
    padding: 10px;
    border: 1px solid #ccc;
}

#result-table {
    width: 100%;
    border-collapse: collapse;
}

#result-table th, #result-table td {
    border: 1px solid #ccc;
    padding: 5px;
}

</style>

