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

        <!-- Boutons de tri et Barre de recherche -->
        <div class="sort-container">
            <button id="sortAsc" class="btn btn-secondary">
                <i class="fas fa-sort-amount-up"></i> Trier par ordre croissant
            </button>
            <button id="sortDesc" class="btn btn-secondary">
                <i class="fas fa-sort-amount-down"></i> Trier par ordre décroissant
            </button>
            <!-- Barre de recherche -->
            <input type="text" id="searchBar" placeholder="Rechercher..." class="searchi-bar" style="display: block;">
        </div>

        <?php
        $groupedRequetes = [];
        foreach ($requetes as $requete) {
            $nomCollection = $requete->getNomCollection();
            if (!isset($groupedRequetes[$nomCollection])) {
                $groupedRequetes[$nomCollection] = [];
            }
            $groupedRequetes[$nomCollection][] = $requete; // Stocker l'objet Requete complet
        }

        // Encodez les données en JSON pour le graphique
        $originalData = json_encode(array_values(array_map('count', $groupedRequetes)));
        $originalLabels = json_encode(array_keys($groupedRequetes));
        ?>

        <!-- Conteneur principal pour le graphique et les enregistrements -->
        <div class="content-container">
            <!-- Graphique -->
            <div id="graphView" class="graph-section">
                <canvas id="meteothequeChart"></canvas>
            </div>

            <!-- Liste des enregistrements -->
            <div id="listView" class="records-section">
                <div class="meteotheque-list" id="meteothequeList">
                    <?php foreach ($groupedRequetes as $nomCollection => $requetesCollection): ?>
                        <div class="meteotheque-item">
                            <strong>Nom de la collection :</strong> <?= htmlspecialchars($nomCollection) ?><br>
                            <strong>Nombre d'enregistrements :</strong> <?= count($requetesCollection) ?><br>

                            <?php if (count($requetesCollection) > 1): ?>
                                <!-- Affichage pour les enregistrements groupés -->
                                <strong>Types d'affichage :</strong><br>
                                <ul>
                                    <li>Carte interactive: <?= floor(count($requetesCollection) / 2) ?></li>
                                    <li>Carte thermique: <?= count($requetesCollection) - floor(count($requetesCollection) / 2) ?></li>
                                </ul>
                            <?php else: ?>
                                <!-- Affichage complet pour un seul enregistrement -->
                                <?php $requete = $requetesCollection[0]; ?>
                                <strong>Date de création :</strong> <?= htmlspecialchars($requete->getDateCreation()) ?><br>
                                <strong>Description :</strong> <?= htmlspecialchars($requete->getDescription()) ?><br>
                                <!-- Ajoutez ici les autres données que vous souhaitez afficher -->
                            <?php endif; ?>

                            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=deleteMeteothequeGroup&controller=meteotheque&nomCollection=<?= urlencode($nomCollection) ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer tous les enregistrements de cette collection ? Cette action est irréversible.');">Supprimer</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Styles pour la disposition côte à côte */
    .content-container {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }

    .graph-section {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    #meteothequeChart {
        max-width: 100%;
        max-height: 400px;
    }

    .records-section {
        flex: 1;
        max-height: 400px; /* Limite la hauteur */
        overflow-y: auto; /* Active le défilement */
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .meteotheque-item {
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
    }

    /* Styles pour la barre de recherche */
    .search-bar {
        width: calc(100% - 20px);
        margin-top: 15px;
        padding: 10px;
        border-radius: 5px;
    }

    /* Styles pour les boutons de tri */
    .sort-container {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let meteothequeChart = null;
    let originalData = <?php echo $originalData; ?>;
    let originalLabels = <?php echo $originalLabels; ?>;

    document.addEventListener("DOMContentLoaded", function() {
        if (!Array.isArray(originalData) || !Array.isArray(originalLabels)) {
            console.error("Les données originales ne sont pas des tableaux valides.");
            return;
        }

        let filteredLabels = [...originalLabels];
        let filteredData = [...originalData];

        // Initialisation du graphique
        function initChart() {
            const ctx = document.getElementById('meteothequeChart').getContext('2d');

            if (meteothequeChart) {
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
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Fonction pour trier les données
        function sortData(ascending = true) {
            const combined = originalLabels.map((label, index) => ({
                label,
                value: originalData[index]
            }));
            combined.sort((a, b) => (ascending ? a.value - b.value : b.value - a.value));

            filteredLabels = combined.map(item => item.label);
            filteredData = combined.map(item => item.value);

            updateListView();

            if (meteothequeChart) {
                meteothequeChart.data.labels = filteredLabels;
                meteothequeChart.data.datasets[0].data = filteredData;
                meteothequeChart.update();
            }
        }

        // Fonction pour filtrer les données
        function filterData(filterText) {
            filteredLabels = [];
            filteredData = [];

            const meteothequeList = document.getElementById('meteothequeList');
            meteothequeList.innerHTML = ''; // Effacer la liste actuelle

            originalLabels.forEach((label, index) => {
                if (label.toLowerCase().includes(filterText)) {
                    filteredLabels.push(label);
                    filteredData.push(originalData[index]);
                }
            });

            updateListView();

            if (meteothequeChart) {
                meteothequeChart.data.labels = filteredLabels;
                meteothequeChart.data.datasets[0].data = filteredData;
                meteothequeChart.update();
            }
        }

        // Fonction pour mettre à jour la liste des enregistrements
        function updateListView() {
            const meteothequeList = document.getElementById('meteothequeList');
            meteothequeList.innerHTML = ''; // Effacer la liste actuelle

            filteredLabels.forEach((label, index) => {
                const nomCollection = label;
                const nombreEnregistrements = filteredData[index];

                const meteothequeItem = document.createElement('div');
                meteothequeItem.classList.add('meteotheque-item');

                let itemContent = `<strong>Nom de la collection :</strong> ${nomCollection}<br>
                                   <strong>Nombre d'enregistrements :</strong> ${nombreEnregistrements}<br>`;

                // Récupérer les requêtes correspondantes à ce nom de collection
                const requetesCollection = <?php echo json_encode($groupedRequetes); ?>[nomCollection];

                if (requetesCollection && requetesCollection.length > 0) {
                    if (requetesCollection.length > 1) {
                        // Affichage pour les enregistrements groupés
                        itemContent += `<strong>Types d'affichage :</strong><br>
                                        <ul>
                                            <li>Carte interactive: ${Math.floor(requetesCollection.length / 2)}</li>
                                            <li>Carte thermique: ${requetesCollection.length - Math.floor(requetesCollection.length / 2)}</li>
                                        </ul>`;
                    } else {
                        // Affichage complet pour un seul enregistrement
                        const requete = requetesCollection[0];
                        itemContent += `<strong>Date de création :</strong> ${requete.dateCreation}<br>
                                        <strong>Description :</strong> ${requete.description}<br>`;
                        // Ajoutez ici les autres données que vous souhaitez afficher
                    }
                }

                meteothequeItem.innerHTML = itemContent;

                meteothequeList.appendChild(meteothequeItem);
            });
        }


        // Gestion de la recherche
        document.getElementById('searchBar').addEventListener('input', function() {
            const filterText = this.value.toLowerCase();
            filterData(filterText);
        });

        // Gestion du tri
        document.getElementById('sortAsc').addEventListener('click', () => sortData(true));
        document.getElementById('sortDesc').addEventListener('click', () => sortData(false));

        // Initialisation du graphique et de la liste au chargement
        initChart();
        updateListView();
    });
</script>