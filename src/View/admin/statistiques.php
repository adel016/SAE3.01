<header class="dashboard-header">
    <h1>Statistiques</h1>
</header>

<br>

<div class="container">
    <!-- Formulaire de filtre -->
    <section class="mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Filtrer les statistiques</h2>
                <form method="post" action="?action=StatistiquesEtLogs&controller=admin" class="row g-3">
                    <div class="col-md-6">
                        <label for="dateDebut" class="form-label">Date de début :</label>
                        <input type="date" name="dateDebut" id="dateDebut" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="dateFin" class="form-label">Date de fin :</label>
                        <input type="date" name="dateFin" id="dateFin" class="form-control">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Statistiques générales -->
    <section class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Nombre d'inscriptions</h5>
                        <p class="display-4 text-primary"><?= $nombreInscriptions ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Nombre de connexions</h5>
                        <p class="display-4 text-success"><?= $nombreConnexions ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Nombre d'ajouts à la Météothèque</h5>
                        <p class="display-4 text-info"><?= $nombreAjoutsMeteotheque ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Nombre de promotions au rôle d'admin</h5>
                        <p class="display-4 text-warning"><?= $nombrePromotions ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Nombre de modifications</h5>
                        <p class="display-4 text-danger"><?= $nombreModifications ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Bouton pour basculer entre tableau et graphique -->
    <div class="toggle-container">
        <span id="toggleLabel">📊 Afficher le Tableau</span>
        <label class="switch">
            <input type="checkbox" id="toggleView">
            <span class="slider"></span>
        </label>
    </div>


    <!-- Tableau des statistiques -->
    <section id="tableauStats" style="display: none;">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Inscriptions</th>
                    <th>Connexions</th>
                    <th>Promotions</th>
                    <th>Modifications</th>
                    <th>Ajouts Météothèque</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscriptionsParJour as $key => $data): ?>
                    <tr>
                        <td><?= htmlspecialchars($data['jour']) ?></td>
                        <td><?= htmlspecialchars($data['total']) ?></td>
                        <td><?= htmlspecialchars($connexionsParJour[$key]['total'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($promotionsParJour[$key]['total'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($modificationsParJour[$key]['total'] ?? 0) ?></td>
                        <td><?= htmlspecialchars($ajoutsMeteothequeParJour[$key]['total'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Graphique -->
    <section id="graphiqueStats">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center">Logs des utilisateurs</h2>
                <canvas id="inscriptionsConnexionsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </section>

</div>

<!-- Script pour le graphique -->
<script>
function extractData(data) {
    return data.map(item => item.total); // Extrait uniquement les valeurs numériques
}

const labels = <?= json_encode(array_column($inscriptionsParJour, 'jour')) ?>; // Extraire les dates

const ctx = document.getElementById('inscriptionsConnexionsChart').getContext('2d');
const inscriptionsConnexionsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Inscriptions',
                data: extractData(<?= json_encode($inscriptionsParJour) ?>),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Connexions',
                data: extractData(<?= json_encode($connexionsParJour) ?>),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },
            {
                label: 'Promotions',
                data: extractData(<?= json_encode($promotionsParJour) ?>),
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            },
            {
                label: 'Modifications',
                data: extractData(<?= json_encode($modificationsParJour) ?>),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            },
            {
                label: 'Ajouts Meteothèque',
                data: extractData(<?= json_encode($ajoutsMeteothequeParJour) ?>),
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                enabled: true
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<script>
document.getElementById('toggleView').addEventListener('change', function() {
    const tableau = document.getElementById('tableauStats');
    const graphique = document.getElementById('graphiqueStats');
    const toggleLabel = document.getElementById('toggleLabel');
    
    if (this.checked) {
        graphique.style.opacity = '0';
        setTimeout(() => {
            graphique.style.display = 'none';
            tableau.style.display = 'block';
            setTimeout(() => {
                tableau.style.opacity = '1';
            }, 10);
        }, 300);
        toggleLabel.innerHTML = "📋 Afficher le Graphique";
    } else {
        tableau.style.opacity = '0';
        setTimeout(() => {
            tableau.style.display = 'none';
            graphique.style.display = 'block';
            setTimeout(() => {
                graphique.style.opacity = '1';
            }, 10);
        }, 300);
        toggleLabel.innerHTML = "📊 Afficher le Tableau";
    }
});
</script>