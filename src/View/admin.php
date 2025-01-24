<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pagetitle ?? "Tableau de bord Admin"); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/css/admin.css" v=<?= time(); ?>>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <!-- Bouton pour ouvrir/fermer la sidebar -->
            <button id="sidebarToggle" class="btn btn-outline-primary me-3">
                ☰
            </button>
            <!-- Titre de la marque -->
            <a class="navbar-brand" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php">MeteoVision</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Liens de navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=default&controller=api">Observations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Météo dans ma ville</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <!-- Profil utilisateur -->
                <?php if (isset($_SESSION['utilisateur_id'])): ?>
                    <div class="d-flex align-items-center">
                        <span class="me-3"><?= htmlspecialchars($_SESSION['prenom'] ?? '') ?> <?= htmlspecialchars($_SESSION['nom'] ?? '') ?></span>
                        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=readProfile&controller=utilisateur">
                            <img src="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/img/profile.jpg" alt="Profil" class="rounded-circle" style="width: 40px; height: 40px;">
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=connexion&controller=utilisateur" class="btn btn-outline-primary">
                        Connexion
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div id="sidebar" class="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php">MeteoVision</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=tableauDeBord&controller=admin">Tableau de bord</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=StatistiquesEtLogs&controller=admin">Statistiques</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=deconnexion&controller=utilisateur">Déconnexion</a>
            </li>
        </ul>
    </div>



    <main class="main-content" id="mainContent">
        <?php
        if (isset($cheminVueBody) && file_exists(__DIR__ . "/$cheminVueBody")) {
            require __DIR__ . "/$cheminVueBody";
        } else {
            echo "<p>Erreur : La vue spécifiée est introuvable ($cheminVueBody).</p>";
        }
        ?>
    </main>

    <div class="message flash-container">
        <?php if (!empty($flashMessages = \App\Meteo\Lib\MessageFlash::lireTousMessages())): ?>
            <?php foreach ($flashMessages as $type => $messages): ?>
                <div class="flash <?= htmlspecialchars($type) ?>">
                    <button class="close-btn" onclick="this.parentElement.remove();">×</button>
                    <?php foreach ($messages as $message): ?>
                        <p><?= htmlspecialchars($message) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <footer>
        <p>© Site METEO VISION - BUT2.C INFORMATIQUE</p>
    </footer>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainContent = document.querySelector('.main-content');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            mainContent.classList.toggle('collapsed');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>