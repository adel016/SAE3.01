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
        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php">MeteoVision</a>
        <a href="#">Tableau de bord</a>
        <a href="#">Observations</a>
        <a href="#">Contact</a>
        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=connexion&controller=utilisateur" class="compte-lien">
            <img src="<?=\App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/img/compte_logo.png" alt="Image de compte" class="compte-image" />
        </a>
    </nav>

    </header>
        
        <?php
        if (isset($cheminVueBody) && file_exists(__DIR__ . "/$cheminVueBody")) {
            require __DIR__ . "/$cheminVueBody";
        } else {
            echo "<p>Erreur : La vue spécifiée est introuvable ($cheminVueBody).</p>";
        }
        ?>

        <div class="flash-messages">
            <?php if (!empty($flashMessages = \App\Meteo\Lib\MessageFlash::lireTousMessages())): ?>
                <?php foreach ($flashMessages as $type => $messages): ?>
                    <div class="alert alert-<?= htmlspecialchars($type) ?>">
                        <?php foreach ($messages as $message): ?>
                            <p><?= htmlspecialchars($message) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['utilisateur_id'])): ?>
            <h2>Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?> !</h2>
        <?php else: ?>
            <h2>Bienvenue sur MeteoVision</h2>
        <?php endif; ?>

    <footer>
        <p>© Site METEO VISION - BUT2.C INFORMATIQUE</p>
    </footer>
</body>
</html>
