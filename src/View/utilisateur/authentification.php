<h1>FORMULAIRE DE CONNEXION / INSCRIPTION</h1>
<button id="toggleButton" onclick="toggleForm()">Inscription</button>

<section class="formulaire">
    <!-- Formulaire de connexion -->
    <div id="connexionForm">
        <h3>Connexion</h3>
        <form action="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/frontController.php?action=connexion&controller=utilisateur" method="POST">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required><br><br>

            <label for="motdepasse">Mot de passe :</label>
            <input type="password" name="motdepasse" id="motdepasse" required><br><br>

            <button type="submit">Se connecter</button>
        </form>
    </div>

    <!-- Formulaire d'inscription -->
    <div id="inscriptionForm">
        <h3>Inscription</h3>
        <form action="/Web/frontController.php?action=inscription&controller=utilisateur" method="POST">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required><br><br>

            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" id="prenom" required><br><br>

            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required><br><br>

            <label for="motdepasse">Mot de passe :</label>
            <input type="password" name="motdepasse" id="motdepasse" required><br><br>

            <button type="submit">S'inscrire</button>
        </form>
    </div>
</section>

<!-- Affichage des messages de succès ou d'erreur -->
<div class="messages">
    <?php if (class_exists('App\Covoiturage\Lib\MessageFlash')): ?>
        <?php foreach (\App\Meteo\Lib\MessageFlash::lireTousMessages() as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <p class="message-<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($message) ?></p>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    // Affiche le formulaire de connexion au départ
    document.getElementById('connexionForm').style.display = 'block';

    // Variable pour garder la trace de l'état actuel (connexion ou inscription)
    let isConnexion = true;

    // Change entre connexion et inscription
    function toggleForm() {
        isConnexion = !isConnexion;

        // Affiche le formulaire correspondant
        document.getElementById('connexionForm').style.display = isConnexion ? 'block' : 'none';
        document.getElementById('inscriptionForm').style.display = isConnexion ? 'none' : 'block';

        // Change le texte du bouton
        document.getElementById('toggleButton').textContent = isConnexion ? 'Inscription' : 'Connexion';
    }
</script>