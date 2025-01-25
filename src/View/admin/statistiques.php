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
                        <h5 class="card-title">Ajouts à la Météothèque</h5>
                        <p class="display-4 text-info"><?= $nombreAjoutsMeteotheque ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Promotions Admin</h5>
                        <p class="display-4 text-warning"><?= $nombrePromotions ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Suppressions d'utilisateur</h5>
                        <p class="display-4 text-danger"><?= $nombreSuppressions ?? 0 ?></p>
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
            labels: ['Inscriptions', 'Connexions', 'Promotions', 'Suppressions', 'Ajouts à la Météothèque'],
            datasets: [
                {
                    label: 'Nombre d\'actions',
                    data: <?= json_encode([
                        $nombreInscriptions,
                        $nombreConnexions,
                        $nombrePromotions,
                        $nombreSuppressions,
                        $nombreAjoutsMeteotheque
                    ]) ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
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

