<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pagetitle ?? 'Liste des utilisateurs' ?></title>
</head>
<body>
    <h1>Liste des utilisateurs</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Role</th>
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
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
