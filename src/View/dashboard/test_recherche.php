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

    <!-- Section des résultats -->
    <div class="result-section">
        <h3>Résultats</h3>
        <div id="result-json"></div>
        <table id="result-table">
            <thead>
                <tr>
                    <th>Région</th>
                    <th>Département</th>
                    <th>Ville</th>
                    <th>Date</th>
                    <th>Vitesse du vent</th>
                    <th>Pression</th>
                    <th>Humidité</th>
                    <th>Température</th>
                    <th>Visibilité</th>
                    <th>Pression au niveau de la mer</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
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
                } else {
                    document.getElementById('result-json').textContent = JSON.stringify(data, null, 2);
                    document.getElementById('result-json').style.display = 'block';
                    table.style.display = 'none';
                }

                // Sauvegarder dans la Météothèque avec le format d'affichage
                saveToMeteotheque({ libgeo, region, nom_dept, date_debut, date_fin, displayMode });
            } else {
                alert('Aucun résultat trouvé.');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des données :', error);
            alert('Une erreur est survenue lors de la récupération des données.');
        });
}

function saveToMeteotheque(data) {
    fetch('<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=saveRequestTableaudeBordJSONouTableau&controller=meteotheque', {
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