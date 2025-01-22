<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin</title>
</head>
<body>
    <h1>Tableau de bord Admin</h1>

    <h2>Liste des utilisateurs</h2>
    <table border="1">
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
                                <button type="submit">Promouvoir Admin</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" action="?action=supprimerUtilisateur&controller=admin" style="display:inline;">
                            <input type="hidden" name="utilisateur_id" value="<?= htmlspecialchars($utilisateur->getId()) ?>">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
