<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <title><?= htmlspecialchars($pagetitle ?? ""); ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/img/MeteoVision_logo.png"/>
    <link rel="stylesheet" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/css/index.css" v=<?= time(); ?>>
</head>
<body>
    <header>
    <nav class="navbar">
        <div class="logo">
            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php" class="logo-text">MétéoVision</a>
        </div>
        <ul class="nav-menu">
            <li class="dropdown">
                <a href="#" class="dropbtn">Tableau de bord</a>
                <div class="dropdown-content">
                    <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=index&controller=tableauDeBord">Recherche graphique</a>
                    <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=rechercheAvancee&controller=tableauDeBord">Recherche libre</a>
                </div>
            </li>
            <li><a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=defaultAction&controller=api">Observations</a></li>
            <?php if ($cheminVueBody === 'observations/carteThermique.php'): ?>
                <script src="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/js/autocomplete.js"></script>
            <?php endif; ?>
            <li><a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=readAll&controller=Meteotheque">Meteotheques</a></li>
            <li><a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=show&controller=contact">Contact</a></li>
        </ul>
        <div class="header-buttons">
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
                <div class="user-info">
                    <span class="user-name">
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'utilisateur'): ?>
                            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=readAll&controller=utilisateur">
                                <?= isset($_SESSION['prenom']) ? htmlspecialchars($_SESSION['prenom']) : '' ?>
                                <?= isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : '' ?>
                            </a>
                        <?php endif; ?>
                    </span>

                    <!-- Vérification si l'utilisateur est admin pour afficher le bouton "Tableau de bord admin" -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=tableauDeBord&controller=admin" class="admin-dashboard-link">
                            Tableau de bord admin
                        </a>
                    <?php endif; ?>

                    <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=deconnexion&controller=utilisateur" class="logout-link">
                        Déconnexion
                    </a>
                </div>
            <?php else: ?>
                <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=connexion&controller=utilisateur" class="compte-lien">
                    <img src="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/img/compte_logo.png" alt="LOGO DE COMPTE" class="compte-image">
                </a>
            <?php endif; ?>
        </div>
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

        <div class="footer-links">
            <a href="#">Politique de confidentialité</a>
            <a href="#">Mentions légales</a>
            <a href="#">Support</a>
        </div>
    </footer>

    <?php if ($cheminVueBody === 'observations/carteThermique.php'): ?>
        <script src="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/js/autocomplete.js"></script>
    <?php endif; ?>
</body>
</html>

<style>
