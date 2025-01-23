<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Styles CSS pour le thème météo */
        body {
            background: linear-gradient(to bottom, #e0f2f7, #bbdefb); /* Dégradé bleu clair */
            font-family: 'Arial', sans-serif;
        }
        .sidebar {
            background-color: rgba(255, 255, 255, 0.8); /* Blanc transparent */
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar h2 {
            color: #2196f3; /* Bleu */
        }
        .sidebar a {
            color: #333;
        }
        .main-content {
            background-color: rgba(255, 255, 255, 0.7);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }
        .table {
            background-color: white;
        }
        .table th, .table td {
            text-align: center; /* Centrer le texte dans les cellules */
        }
        .btn-primary {
            background-color: #2196f3;
            border-color: #2196f3;
        }
        .btn-danger {
            background-color: #f44336;
            border-color: #f44336;
        }
        /* Style pour les icônes */
        .sidebar a i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <h2 class="text-center">Tableau de bord</h2>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#liste-utilisateurs">
                                <i class="fas fa-users"></i> Liste des utilisateurs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=afficherStatistiques&controller=admin">
                                <i class="fas fa-chart-line"></i> Statistiques
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog"></i> Paramètres
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tableau de bord Admin</h1>
                </div>

                <section id="liste-utilisateurs">
                    <h2>Liste des utilisateurs</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($utilisateurs as $utilisateur): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($utilisateur->getId()) ?></td>
                                        <td><?= htmlspecialchars($utilisateur->getNom()) ?></td>
                                        <td><?= htmlspecialchars($utilisateur->getPrenom()) ?></td>
                                        <td><?= htmlspecialchars($utilisateur->getEmail()) ?></td>
                                        <td><?= htmlspecialchars($utilisateur->getRole()) ?></td>
                                        <td>
                                            <?php if ($utilisateur->getRole() !== 'admin'): ?>
                                                <form method="POST" action="?action=promouvoirAdmin&controller=admin" style="display:inline;">
                                                    <input type="hidden" name="utilisateur_id" value="<?= htmlspecialchars($utilisateur->getId()) ?>">
                                                    <button type="submit" class="btn btn-primary btn-sm">Promouvoir Admin</button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="POST" action="?action=supprimerUtilisateur&controller=admin" style="display:inline;">
                                                <input type="hidden" name="utilisateur_id" value="<?= htmlspecialchars($utilisateur->getId()) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>