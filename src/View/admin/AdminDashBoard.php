<header class="bg-primary text-white text-center py-4 mb-4">
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
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
