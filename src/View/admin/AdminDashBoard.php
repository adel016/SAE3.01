<div class="main-content">
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
    <h2 class="dashboard-header">Base de données</h2>

    <!-- Conteneur pour aligner les boutons -->
    <div class="button-container">
        <!-- Bouton Télécharger la base de données -->
        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/download_backup.php" class="btn btn-primary">
            Télécharger la base de données
        </a>

        <!-- Bouton Sauvegarder la base de données -->
        <button id="uploadBackupBtn" class="btn btn-secondary">
            Sauvegarder la base de données
        </button>

        <!-- Bouton pour afficher la liste des anciens fichiers -->
        <button id="showBackupListBtn" class="btn btn-tertiary">
            Afficher les versions précédentes
        </button>
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

    <!-- Conteneur pour la liste des fichiers SQL -->
    <div id="backupListContainer" style="display:none;">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom du fichier</th>
                    <th>Date de modification</th>
                    <th>Télécharger</th>
                </tr>
            </thead>
            <tbody id="backupListBody">
                <!-- Les données seront chargées ici via JavaScript -->
            </tbody>
        </table>
    </div>

    <script>
        // Fonction pour afficher ou masquer la liste des fichiers de sauvegarde
        document.getElementById('showBackupListBtn').addEventListener('click', function() {
            var container = document.getElementById('backupListContainer');
            container.style.display = (container.style.display === 'none') ? 'block' : 'none';
            if (container.style.display === 'block') {
                loadBackupList();
            }
        });

        // Charger la liste des fichiers SQL via AJAX
        function loadBackupList() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_backup_list.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('backupListBody').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>

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

    // Script pour afficher ou masquer le formulaire de téléchargement au clic sur "Sauvegarder la base de données"
    document.getElementById('uploadBackupBtn').addEventListener('click', function () {
        const uploadSection = document.getElementById('upload-section');
        // Basculer entre afficher et masquer
        if (uploadSection.style.display === 'none' || uploadSection.style.display === '') {
            uploadSection.style.display = 'block'; // Afficher la section
        } else {
            uploadSection.style.display = 'none'; // Masquer la section
        }
    });

    // Script pour afficher le message au survol du bouton
    document.getElementById('uploadBackupBtn').addEventListener('mouseover', function () {
        document.getElementById('uploadMessage').style.display = 'block';
    });

    document.getElementById('uploadBackupBtn').addEventListener('mouseout', function () {
        document.getElementById('uploadMessage').style.display = 'none';
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

</div>

<style>

html, body {
    height: 100%; /* Assure que le fond couvre toute la hauteur */
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(to bottom, #FDF7DA 0%, #FFF9E6 15%, #E3F0FF 100%) no-repeat; /* Même couleur que le footer */
    background-attachment: fixed; /* Fixe le fond pour qu'il ne défile pas */
}

.main-content {
    min-height: 100%; /* Assure que le contenu principal remplit la hauteur */
    padding: 20px; /* Ajoute un espace autour du contenu principal */
    box-sizing: border-box; /* Inclut le padding dans la hauteur totale */
}

#sidebarToggle {
    background-color: #3E6F98; /* Couleur bleue */
    color: #fff; /* Couleur du texte en blanc */
    border: none;
    padding: 10px 15px;
    border-radius: 5px; /* Coins arrondis */
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); /* Légère ombre */
    cursor: pointer;
    transition: background-color 0.3s ease;
}

#sidebarToggle:hover {
    background-color: #2C5A7A; /* Bleu légèrement plus foncé au survol */
}
 /* Harmoniser la couleur du header */
 header.dashboard-header, 
.dashboard-header {
    background: transparent; /* Fond transparent */
    color: #3E6F98; /* Couleur du texte */
    padding: 20px;
    text-align: center;
    font-weight: bold;
    border-radius: 10px; /* Coins arrondis pour un effet harmonieux */
    box-shadow: none; /* Supprime l'ombre */
    margin-bottom: 20px; /* Ajoute un espace sous le titre */
}

.table-responsive {
    margin-bottom: 30px; /* Ajoute un espace de 30px sous le tableau */
}

section#sauvegarde-bd {
    margin-bottom: 50px; /* Ajoute un espace sous la section "Base de données" */
}

#backupListContainer {
    margin-top: 20px; /* Ajoute un espace de 20px au-dessus du tableau */
}

/* Conteneur des boutons */
.button-container {
    display: flex;
    justify-content: center; /* Espacement uniforme entre les boutons */
    align-items: center;
    gap: 10px; /* Espacement entre les boutons */
    margin-top: 20px;
}

/* Styles des boutons */
.btn {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Couleurs harmonisées pour les boutons */
.btn-primary {
    background-color: #4CAF50; /* Vert clair */
}

.btn-primary:hover {
    background-color: #45A049; /* Vert légèrement plus foncé */
}

.btn-secondary {
    background-color: #2196F3; /* Bleu clair */
}

.btn-secondary:hover {
    background-color: #1E88E5; /* Bleu légèrement plus foncé */
}

.btn-tertiary {
    background-color: #FFC107; /* Jaune clair */
}

.btn-tertiary:hover {
    background-color: #FFB300; /* Jaune légèrement plus foncé */
}

/* Footer styles */
footer {
    margin-top: 20px; /* Réduire l'espace au-dessus */
    text-align: center;
    padding: 15px 10px; /* Réduire le padding */
    font-size: 14px; /* Réduire la taille de la police */
    background: linear-gradient(to bottom, #FDF7DA 0%, #FFF9E6 15%, #E3F0FF 100%) no-repeat;
    border-radius: 10px; /* Réduire le rayon des coins */
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); /* Réduire l'ombre */
    color: #333;
}

footer p {
    margin: 5px 0; /* Réduire les marges */
    font-size: 20px;
    font-family: 'Arial', sans-serif;
    color: #3E6F98;
}

.footer-links {
    display: flex;
    justify-content: center;
    gap: 10px; /* Réduire l'espacement entre les liens */
    margin-top: 10px; /* Réduire l'espace au-dessus */
}

.footer-links a {
    font-size: 14px; /* Réduire la taille de la police */
    padding: 5px 8px; /* Réduire le padding des liens */
    border-radius: 5px; /* Réduire le rayon des coins */
    background-color: #E3F0FF;
    transition: background-color 0.3s ease;
    text-decoration: none;
    color: #3E6F98;
}

.footer-links a:hover {
    background-color: #D0E1F9;
}
</style>