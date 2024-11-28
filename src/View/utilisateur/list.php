<!DOCTYPE html>
<html>
<head>
    <title>Liste des utilisateurs</title>
</head>
<body>
    <h1>Liste des utilisateurs</h1>
    <?php if (isset($utilisateurs) && is_array($utilisateurs)): ?>
        <ul>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <li><?= htmlspecialchars($utilisateur->getNom()) ?> (<?= htmlspecialchars($utilisateur->getEmail()) ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun utilisateur trouvé.</p>
    <?php endif; ?>
</body>
</html>
