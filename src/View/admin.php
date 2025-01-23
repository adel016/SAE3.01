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
    <header>
        <div class="sidebar" id="sidebar">
            <button id="sidebarToggle" class="cl-sidebar">☰</button>
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

        <div class="main-content" id="mainContent">
            <nav class="navbar bg-light">
                <h1>Bienvenue dans MeteoVision</h1>
            </nav>
        </div>
    </header>

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
        const mainContent = document.getElementById('mainContent');
        const main = document.getElementById('main'); // Ajout de la balise <main>
        const toggleButton = document.getElementById('sidebarToggle');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
            main.classList.toggle('collapsed'); // Ajout de la classe pour <main>
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>