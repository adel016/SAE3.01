<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion / Inscription</title>
    <style>
        #inscriptionForm, #connexionForm {
            display: none;
        }
    </style>
</head>
<body>
    <h2>Bienvenue</h2>
    <button id="toggleButton" onclick="toggleForm()">Inscription</button>

    <!-- Formulaire de connexion -->
    <div id="connexionForm">
        <h3>Connexion</h3>
        <form action="../Controller/ControllerUtilisateur.php?action=connexion" method="POST">
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
        <form action="http://localhost/SAE3.01/src/Controller/ControllerUtilisateur.php?action=inscription" method="POST">
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

    <script>
        let isInscription = true;

        // Affiche le formulaire d'inscription par défaut
        document.getElementById('inscriptionForm').style.display = 'block';

        // Change entre inscription et connexion
        function toggleForm() {
            isInscription = !isInscription;

            document.getElementById('inscriptionForm').style.display = isInscription ? 'block' : 'none';
            document.getElementById('connexionForm').style.display = isInscription ? 'none' : 'block';

            document.getElementById('toggleButton').textContent = isInscription ? 'Connexion' : 'Inscription';
        }
    </script>
</body>
</html>
