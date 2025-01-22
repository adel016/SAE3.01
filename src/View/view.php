<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <title><?= htmlspecialchars($pagetitle ?? ""); ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/css/index.css"v=<?= time(); ?>>
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php" class="title">MeteoVision</a>
            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=readAll&controller=utilisateur">Tableau de bord</a>
            <a href="#">Observations</a>
            <a href="#">Météo dans ma ville</a>
            <a href="#">Contact</a>

            <?php if (isset($_SESSION['utilisateur_id'])): ?>
                <!-- Lien de déconnexion -->
                <div class="user-info">
                    <span class="user-name">
                        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=readAll&controller=utilisateur">
                            <?= isset($_SESSION['prenom']) ? htmlspecialchars($_SESSION['prenom']) : '' ?> 
                            <?= isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : '' ?>
                        </a>
                    </span>
                    <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=deconnexion&controller=utilisateur" class="logout-link">
                        Déconnexion
                    </a>
                </div>
            <?php else: ?>
                <!-- Lien de connexion si l'utilisateur n'est pas connecté -->
                <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=connexion&controller=utilisateur" class="compte-lien">
                    <img src="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/img/compte_logo.png" alt="LOGO DE COMPTE" class="compte-image">
                </a>
            <?php endif; ?>
        </nav>
    </header>

        <?php
        if (isset($cheminVueBody) && file_exists(__DIR__ . "/$cheminVueBody")) {
            require __DIR__ . "/$cheminVueBody";
        } else {
            echo "<p>Erreur : La vue spécifiée est introuvable ($cheminVueBody).</p>";
        }
        ?>

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
</body>
</html>
