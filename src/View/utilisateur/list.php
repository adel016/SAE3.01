<h1>Liste des utilisateurs</h1>
<?php if (isset($utilisateurs) && is_array($utilisateurs)): ?>
    <ul>
        <?php foreach ($utilisateurs as $utilisateur): ?>
            <li>
                <span><?= htmlspecialchars($utilisateur->getNom()) ?> (<?= htmlspecialchars($utilisateur->getEmail()) ?>)</span>
                - Rôle: <span><?= htmlspecialchars($utilisateur->getRole()) ?></span>
                <a href="/Web/frontController.php?action=update&controller=utilisateur&id=<?= $utilisateur->getId() ?>">Modifier</a>
                <a href="/Web/frontController.php?action=delete&controller=utilisateur&id=<?= $utilisateur->getId() ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                <a href="/Web/frontController.php?action=changerRole&controller=utilisateur&id=<?= $utilisateur->getId() ?>">Changer le rôle</a>
            </li>
        <?php endforeach; ?>
       </ul>
<?php else: ?>
    <p>Aucun utilisateur trouvé.</p>
<?php endif; ?>