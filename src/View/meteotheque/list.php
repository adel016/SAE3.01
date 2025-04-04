<div class="profile-container">
    <!-- Sélection de l'utilisateur -->
    <h1>Meteothèque des Utilisateurs</h1>
    <p style="font-size: 1.25rem;">
        Bienvenue sur la page Météothèques !
        Cette interface dédiée à la gestion des données météorologiques permet de consulter 
        et d’analyser les enregistrements des utilisateurs de manière structurée et visuelle. 
        Conçue pour les besoins spécifiques de la météorologie, 
        elle combine simplicité d’utilisation et fonctionnalités avancées.
    </p>
    <select id="selectUtilisateur">
        <option value="">-- Choisir un utilisateur --</option>
        <?php foreach ($utilisateurs as $utilisateur) { ?>
            <option value="<?= htmlspecialchars($utilisateur['id']) ?>">
                <?= htmlspecialchars($utilisateur['nom']) ?> <?= htmlspecialchars($utilisateur['prenom']) ?> (ID: <?= htmlspecialchars($utilisateur['id']) ?>)
            </option>
        <?php } ?>
    </select>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button id="viewMeteotheque" class="btn btn-primary">Voir Météothèque</button>
        <button id="resetPage" class="btn btn-secondary">Réinitialiser</button>
    </div>

    <!-- Boutons de tri et Barre de recherche -->
    <div class="sort-container">
        <button id="sortAsc" class="btn btn-secondary">
            <i class="fas fa-sort-amount-up"></i> Trier par ordre croissant
        </button>
        <button id="sortDesc" class="btn btn-secondary">
            <i class="fas fa-sort-amount-down"></i> Trier par ordre décroissant
        </button>
        <!-- Barre de recherche -->
        <input type="text" id="searchBar" placeholder="Rechercher..." class="searchi-bar" style="display: none;">
    </div>

    <!-- Titre pour afficher le nom de l'utilisateur -->
    <h2 id="resultTitle" style="display: none;"></h2>

    <!-- Conteneur principal pour le graphique et les enregistrements -->
    <div class="content-container">
        <!-- Graphique -->
        <div id="graphView" class="graph-section">
            <canvas id="meteothequeChart"></canvas>
        </div>

        <!-- Liste des enregistrements -->
        <div id="meteothequeContent" class="records-section"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let meteothequeChart = null;
let originalData = [],
    originalLabels = [],
    filteredLabels = [],
    filteredData = [];

async function loadMeteotheque(userId) {
    try {
        const url = `<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=readMeteothequeByUser&controller=Meteotheque&user_id=${userId}`;
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`Erreur HTTP : ${response.status}`);
        }

        const data = await response.json();
        console.log("Données JSON analysées :", data);

        if (!data.success || !data.user || !data.results) {
            alert('Erreur : Les données reçues sont incomplètes.');
            return;
        }

        // Regrouper les enregistrements par nom de collection et stocker les données complètes
        const groupedData = data.results.reduce((acc, item) => {
            const key = item.nom_collection;
            if (!acc[key]) {
                acc[key] = [];
            }
            acc[key].push(item); // Stocker l'objet complet
            return acc;
        }, {});

        // Affichage du titre et des données
        document.getElementById('resultTitle').innerText = `Meteothèque de ${data.user.nom} ${data.user.prenom}`;
        document.getElementById('resultTitle').style.display = 'block';

        let meteothequeHTML = '';
        for (const nomCollection in groupedData) {
            const enregistrements = groupedData[nomCollection];
            const nombreEnregistrements = enregistrements.length;

            meteothequeHTML += `
                <div class='meteotheque-item'>
                    <strong>Nom :</strong> ${nomCollection}<br>
                    <strong>Nombre d'enregistrements :</strong> ${nombreEnregistrements}<br>
            `;

            if (nombreEnregistrements > 1) {
                // Affichage pour les enregistrements groupés
                meteothequeHTML += `<strong>Types d'affichage :</strong><br>`;
                meteothequeHTML += `
                    <ul>
                        <li>Carte interactive: ${Math.floor(nombreEnregistrements / 2)}</li>
                        <li>Carte thermique: ${nombreEnregistrements - Math.floor(nombreEnregistrements / 2)}</li>
                    </ul>
                `;
            } else {
                // Affichage complet pour un seul enregistrement
                const enregistrement = enregistrements[0];
                meteothequeHTML += `
                    <strong>Date de création :</strong> ${enregistrement.date_creation}<br>
                    <strong>Description :</strong> ${enregistrement.description}<br>
                    <!-- Ajoutez ici les autres données que vous souhaitez afficher -->
                `;
            }

            meteothequeHTML += `</div>`; // Fin de meteotheque-item
        }

        document.getElementById('meteothequeContent').innerHTML = meteothequeHTML;
        document.getElementById('meteothequeContent').style.display = 'block';
        document.getElementById('searchBar').style.display = 'block';

        // Gestion des données du graphique
        originalLabels = Object.keys(groupedData);
        originalData = Object.values(groupedData).map(enregistrements => enregistrements.length);
        filteredLabels = [...originalLabels];
        filteredData = [...originalData];

        document.querySelector('.sort-container').style.display = 'flex';
        document.getElementById('graphView').style.display = 'block'; // Toujours afficher
        initChart();

    } catch (error) {
        console.error('Erreur lors du chargement des données :', error);
        alert('Erreur lors du chargement des données.');
    }
}

function initChart() {
    // Supprimer l'ancien canvas s'il existe
    const oldCanvas = document.getElementById('meteothequeChart');
    if (oldCanvas) oldCanvas.remove();

    // Créer un nouveau canvas
    const newCanvas = document.createElement('canvas');
    newCanvas.id = 'meteothequeChart';
    document.getElementById('graphView').appendChild(newCanvas);

    const ctx = newCanvas.getContext('2d');
    if (!ctx) {
        console.error("Impossible d'obtenir le contexte 2D du canvas.");
        return;
    }

    // Vérification des données
    if (filteredLabels.length === 0 || filteredData.length === 0) {
        console.warn('Les données pour le graphique sont vides.');
        return;
    }

    // Créer le graphique
    meteothequeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: filteredLabels,
            datasets: [{
                label: 'Nombre d\'enregistrements',
                data: filteredData,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

document.getElementById('viewMeteotheque').addEventListener('click', function() {
    const userId = document.getElementById('selectUtilisateur').value;
    if (userId) {
        loadMeteotheque(userId);
    }
});

document.getElementById('sortAsc').addEventListener('click', () => sortData(true));
document.getElementById('sortDesc').addEventListener('click', () => sortData(false));

function sortData(ascending = true) {
    const combined = originalLabels.map((label, index) => ({ label, value: originalData[index] }));
    combined.sort((a, b) => (ascending ? a.value - b.value : b.value - a.value));

    filteredLabels = combined.map(item => item.label);
    filteredData = combined.map(item => item.value);

    // Mettre à jour l'affichage des enregistrements
    const items = document.querySelectorAll('.meteotheque-item');
    items.forEach(item => item.remove());
    filteredLabels.forEach((label, index) => {
        const item = document.createElement('div');
        item.classList.add('meteotheque-item');
        item.innerHTML = `
            <strong>Nom :</strong> ${label}<br>
            <strong>Nombre d'enregistrements :</strong> ${filteredData[index]}
        `;
        document.getElementById('meteothequeContent').appendChild(item);
    });

    if (meteothequeChart) {
        meteothequeChart.data.labels = filteredLabels;
        meteothequeChart.data.datasets[0].data = filteredData;
        meteothequeChart.update();
    }
}

document.getElementById('searchBar').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const items = document.querySelectorAll('.meteotheque-item');
    filteredLabels = [];
    filteredData = [];

    items.forEach((item, index) => {
        const label = originalLabels[index];
        const match = label.toLowerCase().includes(filter);

        item.style.display = match ? '' : 'none';

        if (match) {
            filteredLabels.push(label);
            filteredData.push(originalData[index]);
        }
    });

    if (meteothequeChart) {
        meteothequeChart.data.labels = filteredLabels;
        meteothequeChart.data.datasets[0].data = filteredData;
        meteothequeChart.update();
    }
});

document.getElementById('resetPage').addEventListener('click', () => {
    document.getElementById('selectUtilisateur').value = "";
    document.getElementById('resultTitle').style.display = 'none';
    document.getElementById('meteothequeContent').style.display = 'none';
    document.getElementById('graphView').style.display = 'none';
    document.querySelector('.sort-container').style.display = 'none';
    document.getElementById('searchBar').style.display = 'none';
});
</script>