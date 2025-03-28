<!-- filepath: /c:/wamp64/www/SAE3.01/src/View/admin/AdminDashBoard.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
    <link rel="stylesheet" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/css/index.css" v=<?= time(); ?>>
    <link rel="stylesheet" href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Assets/css/admin.css" v=<?= time(); ?>>
</head>
<body>
    <header class="dashboard-header">
        <h1>Tableau de bord</h1>
    </header>

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
                                <button class="btn btn-info btn-sm voir-meteotheque" data-id="<?= htmlspecialchars($utilisateur->getId()) ?>">Voir Météothèque</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="meteotheque-container" class="mt-4" style="display: none;">
            <h3>Météothèque de l'utilisateur</h3>
            <div id="meteotheque-content">
                <!-- Les informations sur la météothèque seront insérées ici -->
            </div>
            <button id="hide-meteotheque" class="btn btn-secondary mt-2">Cacher Météothèque</button>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const meteothequeButtons = document.querySelectorAll('.voir-meteotheque');
        const meteothequeContainer = document.getElementById('meteotheque-container');
        const meteothequeContent = document.getElementById('meteotheque-content');
        const hideButton = document.getElementById('hide-meteotheque');

        let activeUserId = null;

        meteothequeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const userId = button.getAttribute('data-id');

                // Si on clique sur le même utilisateur, on masque la météothèque
                if (activeUserId === userId) {
                    meteothequeContainer.style.display = 'none';
                    activeUserId = null;
                    return;
                }

                activeUserId = userId;

                // Requête pour récupérer les données de la météothèque
                fetch(`?action=getMeteotheque&controller=admin&user_id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Insérer les données de la météothèque
                            meteothequeContent.innerHTML = `
                                <ul class="list-group">
                                    ${data.requetes.map(req => `
                                        <li class="list-group-item">
                                            <strong>Nom de la collection :</strong> ${req.nomCollection}<br>
                                            <strong>Description :</strong> ${req.description}<br>
                                            <strong>Date :</strong> ${req.dateCreation}
                                        </li>
                                    `).join('')}
                                </ul>
                            `;
                            meteothequeContainer.style.display = 'block';
                        } else {
                            meteothequeContent.innerHTML = `<p class="text-danger">Aucune donnée trouvée pour cet utilisateur.</p>`;
                            meteothequeContainer.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement de la météothèque :', error);
                        meteothequeContent.innerHTML = `<p class="text-danger">Erreur lors du chargement des données.</p>`;
                        meteothequeContainer.style.display = 'block';
                    });
            });
        });

        // Masquer la météothèque
        hideButton.addEventListener('click', () => {
            meteothequeContainer.style.display = 'none';
            activeUserId = null;
        });
    });
    </script>


   <!-- Section Sauvegarde de la base de données -->
<section id="sauvegarde-bd">
    <h2>Base de données</h2>

    <!-- Bouton Télécharger la base de données -->
    <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/download_backup.php" class="btn btn-success">
        Télécharger la base de données
    </a>

    <!-- Bouton Sauvegarder la base de données -->
    <button id="uploadBackupBtn" class="btn btn-warning mt-3">
        Sauvegarder la base de données
    </button>

    <!-- Message d'info pour l'upload (apparaît au survol du bouton) -->
    <div id="uploadMessage" style="display: none; font-size: 12px; color: #555; margin-top: 10px;">
        Uploader une nouvelle version de la base de données pour les sauvegardes administrateur.
    </div>

    <!-- Formulaire d'upload de la base de données -->
    <div id="upload-section" style="display: none; margin-top: 20px;">
        <h3>Uploader une nouvelle version de la base de données</h3>
        <form id="upload-form" action="upload_backup.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file-upload" class="form-label">Choisissez le fichier SQL :</label>
                <input type="file" id="file-upload" name="backup-file" accept=".sql" class="form-control">
            </div>
            <button type="submit" class="btn btn-warning mt-2">Télécharger</button>
        </form>
    </div>
</section>

<!-- Notification Toast -->
<div id="toast-notification" class="toast hidden">
    <p id="toast-message"></p>
</div>

<script>
    // Script pour afficher le message au survol du bouton
    document.getElementById('uploadBackupBtn').addEventListener('mouseover', function() {
        document.getElementById('uploadMessage').style.display = 'block';
    });

    document.getElementById('uploadBackupBtn').addEventListener('mouseout', function() {
        document.getElementById('uploadMessage').style.display = 'none';
    });

    // Script pour afficher le formulaire de téléchargement au clic sur "Sauvegarder la base de données"
    document.getElementById('uploadBackupBtn').addEventListener('click', function() {
        // Afficher le formulaire d'upload de fichier
        document.getElementById('upload-section').style.display = 'block';
    });

    // Script de notification Toast
    function showToast(message) {
        const toast = document.getElementById("toast-notification");
        const toastMessage = document.getElementById("toast-message");

        toastMessage.innerText = message;
        toast.classList.add("show");

        setTimeout(() => {
            toast.classList.remove("show");
        }, 3000); // Cache la notification après 3 secondes
    }

    document.addEventListener("DOMContentLoaded", function () {
        const downloadBtn = document.querySelector("a.btn-success"); // ✅ Sélectionne le bon bouton

        if (downloadBtn) {
            downloadBtn.addEventListener("click", function (event) {
                event.preventDefault(); // Évite le téléchargement immédiat pour voir le toast
                showToast("Sauvegarde de la base de données MeteoVision réussie ✅");

                // Lancer le téléchargement après 1 seconde pour l'effet
                setTimeout(() => {
                    window.location.href = downloadBtn.href;
                }, 1000);
            });
        }
    });
</script>


    

</body>
</html>