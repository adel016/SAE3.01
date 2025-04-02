<div class="profile-container">
    <!-- Section Profil -->
    <section class="user-profile">
        <h1>Bienvenue sur votre profil, <?= htmlspecialchars($utilisateur->getPrenom()) ?> !</h1>

        <div class="user-details">
            <p><strong>Nom :</strong> <?= htmlspecialchars($utilisateur->getNom()) ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($utilisateur->getPrenom()) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($utilisateur->getEmail()) ?></p>
            <p><strong>Rôle :</strong> <?= htmlspecialchars($utilisateur->getRole()) ?></p>
        </div>

        <div class="profile-actions">
            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=update&controller=utilisateur&id=<?= $utilisateur->getId() ?>" class="btn btn-primary">Modifier mon profil</a>
            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=delete&controller=utilisateur&id=<?= $utilisateur->getId() ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">Supprimer mon compte</a>
        </div>
    </section>

    <!-- Section Meteothèque -->
    <section class="meteotheque">
        <h2>Votre Meteothèque</h2>
        <input type="text" id="searchBar" placeholder="Rechercher..." class="search-bar">
        <div class="toggle-container">
            <input type="checkbox" id="toggleView" class="toggle-input">
            <label for="toggleView" class="toggle-label"></label>
            <span class="toggle-text">Appuyez ici pour le voir en graphique</span>
        </div>
        <div class="sort-container">
            <button id="sortAsc" class="btn btn-secondary">Trier par ordre croissant</button>
            <button id="sortDesc" class="btn btn-secondary">Trier par ordre décroissant</button>
        </div>


        <?php
        $groupedRequetes = [];
        foreach ($requetes as $requete) {
            $nomCollection = $requete->getNomCollection();
            if (!isset($groupedRequetes[$nomCollection])) {
                $groupedRequetes[$nomCollection] = [];
            }
            $groupedRequetes[$nomCollection][] = $requete->getDateCreation();
        }

        // Encodez les données en JSON
        $originalData = json_encode(array_values(array_map('count', $groupedRequetes)));
        $originalLabels = json_encode(array_values(array_keys($groupedRequetes)));
        ?>

        <div id="graphView" class="content-slide">
            <canvas id="meteothequeChart"></canvas>
        </div>

        <div id="listView" class="content-slide active">
            <div class="meteotheque-list" id="meteothequeList">
                <?php foreach ($groupedRequetes as $nomCollection => $dates): ?>
                    <div class="meteotheque-item">
                        <strong>Nom de la collection :</strong> <?= htmlspecialchars($nomCollection) ?><br>
                        <strong>Nombre d'enregistrements :</strong> <?= count($dates) ?><br>
                        <strong>Dates :</strong> <?= implode(' || ', array_map('htmlspecialchars', $dates)) ?><br>
                        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=deleteMeteothequeGroup&controller=meteotheque&nomCollection=<?= urlencode($nomCollection) ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer tous les enregistrements de cette collection ? Cette action est irréversible.');">Supprimer</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </section>
</div>

<style>
.search-bar {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.toggle-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.toggle-input {
    display: none;
}

.toggle-label {
    width: 50px;
    height: 25px;
    background: #ddd;
    border-radius: 25px;
    position: relative;
    cursor: pointer;
    transition: background 0.3s;
    margin-right: 10px;
}

.toggle-label::before {
    content: "";
    width: 20px;
    height: 20px;
    background: white;
    position: absolute;
    top: 50%;
    left: 5px;
    transform: translateY(-50%);
    border-radius: 50%;
    transition: left 0.3s;
}

.toggle-input:checked + .toggle-label {
    background: #007bff;
}

.toggle-input:checked + .toggle-label::before {
    left: 25px;
}

.toggle-text {
    font-size: 14px;
    color: #333;
}

.content-slide {
    display: none;
    height: auto;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.content-slide.active {
    display: block;
    opacity: 1;
}

.sort-container {
    display: none;
    margin-bottom: 15px;
    gap: 10px;
}

.sort-container.active {
    display: flex; 
    justify-content: center;
}

#meteothequeChart {
    max-height: 100%;
    width: 100% !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let meteothequeChart = null;
let originalData = <?php echo $originalData; ?>;
let originalLabels = <?php echo $originalLabels; ?>;

if (!Array.isArray(originalData) || !Array.isArray(originalLabels)) {
    console.error("Les données originales ne sont pas des tableaux valides.");
} else {
    let filteredLabels = [...originalLabels];
    let filteredData = [...originalData];

    // Initialisation du graphique
    function initChart() {
        const ctx = document.getElementById('meteothequeChart').getContext('2d');

        if(meteothequeChart) {
            meteothequeChart.destroy();
        }

        meteothequeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: filteredLabels,
                datasets: [{
                    label: "Nombre d'enregistrements",
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
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Fonction pour trier les données
    function sortData(ascending = true) {
        const combined = originalLabels.map((label, index) => ({ label, value: originalData[index] }));
        combined.sort((a, b) => (ascending ? a.value - b.value : b.value - a.value));

        filteredLabels = combined.map(item => item.label);
        filteredData = combined.map(item => item.value);

        if(meteothequeChart) {
            meteothequeChart.data.labels = filteredLabels;
            meteothequeChart.data.datasets[0].data = filteredData;
            meteothequeChart.update();
        }
    }

    // Gestion du toggle
    document.getElementById('toggleView').addEventListener('change', function() {
        const graphView = document.getElementById('graphView');
        const listView = document.getElementById('listView');
        const sortContainer = document.querySelector('.sort-container');

        if (this.checked) {
            listView.classList.remove('active');
            graphView.classList.add('active');
            sortContainer.classList.add('active'); // Afficher les boutons de tri
            initChart(); // Réinitialiser le graphique avec les données filtrées
        } else {
            graphView.classList.remove('active');
            listView.classList.add('active');
            sortContainer.classList.remove('active'); // Masquer les boutons de tri
        }
    });

    // Gestion de la recherche
    document.getElementById('searchBar').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const items = document.querySelectorAll('.meteotheque-item');
        filteredLabels = [];
        filteredData = [];

        items.forEach((item, index) => {
            const label = originalLabels[index];
            const match = label.toLowerCase().includes(filter);

            item.style.display = match ? '' : 'none';

            if(match) {
                filteredLabels.push(label);
                filteredData.push(originalData[index]);
            }
        });

        if(meteothequeChart) {
            meteothequeChart.data.labels = filteredLabels;
            meteothequeChart.data.datasets[0].data = filteredData;
            meteothequeChart.update();
        }
    });

    // Gestion du tri
    document.getElementById('sortAsc').addEventListener('click', () => sortData(true));
    document.getElementById('sortDesc').addEventListener('click', () => sortData(false));

    // Initialisation au chargement si nécessaire
    document.addEventListener("DOMContentLoaded", function() {
        if(document.getElementById('toggleView').checked) {
            initChart();
        }
    });
}
</script>