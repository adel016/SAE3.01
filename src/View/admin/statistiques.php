<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <!-- En-tête -->
    <header class="bg-primary text-white text-center py-4 mb-4">
        <h1>Statistiques</h1>
    </header>

    <div class="container">
        <!-- Formulaire de filtre -->
        <section class="mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">Filtrer les statistiques</h2>
                    <form method="post" action="?action=afficherStatistiques&controller=admin" class="row g-3">
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
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Nombre d'inscriptions</h5>
                            <p class="display-4 text-primary"><?= $nombreInscriptions ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Nombre de connexions</h5>
                            <p class="display-4 text-success"><?= $nombreConnexions ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Logs -->
        <section class="mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">Logs</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Utilisateur ID</th>
                                    <th>Action</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= $log->getLogId() ?></td>
                                        <td><?= $log->getUtilisateurId() ?></td>
                                        <td><?= $log->getAction() ?></td>
                                        <td><?= $log->getTimestamp() ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                labels: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
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

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+3i4l6Y1zV2w8KVpH2X4pGNI5D7h2" crossorigin="anonymous"></script>
</body>
</html>
