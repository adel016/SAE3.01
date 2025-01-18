<h1>Liste des utilisateurs</h1>
<?php if (isset($utilisateurs) && is_array($utilisateurs)): ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <tr>
                    <td><?= htmlspecialchars($utilisateur->getNom()) ?></td>
                    <td><?= htmlspecialchars($utilisateur->getPrenom()) ?></td>
                    <td><?= htmlspecialchars($utilisateur->getEmail()) ?></td>
                    <td><?= htmlspecialchars($utilisateur->getRole()) ?></td>
                    <td>
                        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=update&controller=utilisateur&id=<?= $utilisateur->getId() ?>" class="user">Modifier l'utilisateur</a>
                        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=changerRole&controller=utilisateur&id=<?= $utilisateur->getId() ?>" class="user">Modifier le rôle</a>
                        <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=delete&controller=utilisateur&id=<?= $utilisateur->getId() ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');" class="user danger">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun utilisateur trouvé.</p>
<?php endif; ?>
