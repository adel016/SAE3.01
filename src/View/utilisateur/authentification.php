<div class="log-container">
    <h1>Bienvenue sur MétéoVision !</h1>

        <section class="formulaire">
            <!-- Formulaire de connexion -->
            <div id="connexionForm">
                <form action="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=connexion&controller=utilisateur" method="POST">
                    <label for="emailConnexion">Email :</label>
                    <input type="email" name="email" id="emailConnexion" required><br><br>

                    <label for="motdepasseConnexion">Mot de passe :</label>
                    <input type="password" name="motdepasse" id="motdepasseConnexion" required><br><br>

                    <button type="submit">Se connecter</button>
                </form>
                <p>Vous n'avez pas de compte ? <a href="#" onclick="toggleForm('inscription')">Inscrivez-vous!</a></p>
            </div>

            <!-- Formulaire d'inscription -->
            <div id="inscriptionForm" style="display: none;">
                <form action="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=inscription&controller=utilisateur" method="POST">
                    <div class="name-fields">
                        <div>
                            <label for="nom">Nom :</label>
                            <input type="text" name="nom" id="nom" required>
                        </div>
                        <div>
                            <label for="prenom">Prénom :</label>
                            <input type="text" name="prenom" id="prenom" required>
                        </div>
                    </div>
                    <br>
                    <label for="emailInscription">Email :</label>
                    <input type="email" name="email" id="emailInscription" required><br><br>

                    <label for="motdepasseInscription">Mot de passe :</label>
                    <input type="password" name="motdepasse" id="motdepasseInscription" required><br><br>

                    <button type="submit">S'inscrire</button>
                </form>
                <p>Vous avez déjà un compte ? <a href="#" onclick="toggleForm('connexion')">Connectez-vous!</a></p>
            </div>
        </section>
    </div>

    <script>
        // Affiche le formulaire de connexion au départ
        document.getElementById('connexionForm').style.display = 'block';
        document.getElementById('inscriptionForm').style.display = 'none';

        // Change entre connexion et inscription
        function toggleForm(formType) {
            if (formType === 'inscription') {
                document.getElementById('connexionForm').style.display = 'none';
                document.getElementById('inscriptionForm').style.display = 'block';
            } else {
                document.getElementById('connexionForm').style.display = 'block';
                document.getElementById('inscriptionForm').style.display = 'none';
            }
        }
    </script>