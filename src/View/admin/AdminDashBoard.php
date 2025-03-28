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
