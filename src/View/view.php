<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title><?= htmlspecialchars($pagetitle); ?></title>
        <link rel="stylesheet" href="/Assets/css/style.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:ital,wght@0,300..800;1,300..800&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    </head>
    <body>
        <header>
            <nav>
                <h1>SITE DE METEO</h1>
                <a href="">BARRE DE NAVIGATION</a>
            </nav>
        </header>
        <main>
            &nbsp;
            <?php
            require __DIR__ . "/{$cheminVueBody}";
            if (file_exists($vuePath)) {
                require $vuePath;
            } else {
                echo "<p>Erreur : La vue specifiee est introuvable ($cheminVueBody).</p>";
            }
            ?>
            &nbsp;
        </main>
        <footer>
            <p>© Site DATA METEO - BUT2.C INFORMATIQUE </p>
        </footer>
    </body>
</html>