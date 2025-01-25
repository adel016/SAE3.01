<header class="bg-primary text-white text-center py-4 mb-4">
    <h1>Statistiques</h1>
</header>

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


    <!-- Logs -->
    <section class="mb-4">
        <h2>Logs</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars($log->getLogId()) ?></td>
                        <td><?= htmlspecialchars($log->getUtilisateurId()) ?></td>
                        <td><?= htmlspecialchars($log->getAction()) ?></td>
                        <td><?= htmlspecialchars($log->getTimestamp()) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Graphiques -->
    <section>
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title text-center">Graphiques</h2>
                <canvas id="inscriptionsConnexionsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </section>
</div>

<!-- Script pour le graphique -->
<script>
const ctx = document.getElementById('inscriptionsConnexionsChart').getContext('2d');
const inscriptionsConnexionsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        datasets: [
            {
                label: 'Inscriptions',
                data: <?= json_encode($inscriptionsParJour) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Connexions',
                data: <?= json_encode($connexionsParJour) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },
            {
                label: 'Promotions',
                data: <?= json_encode($promotionsParJour) ?>,
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            },
            {
                label: 'Modifications',
                data: <?= json_encode($modificationsParJour) ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            },
            {
                label: 'Ajouts Meteothèque',
                data: <?= json_encode($ajoutsMeteothequeParJour) ?>,
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