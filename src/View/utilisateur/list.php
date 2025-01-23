<div class="profile-container">
    <h1>Bienvenue sur votre profil, <?= htmlspecialchars($utilisateur->getPrenom()) ?> !</h1>

    <?php if ($utilisateur): ?>
        <div class="user-details">
            <p><strong>Nom :</strong> <?= htmlspecialchars($utilisateur->getNom()) ?></p>
            <p><strong>Prénom :</strong> <?= htmlspecialchars($utilisateur->getPrenom()) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($utilisateur->getEmail()) ?></p>
            <p><strong>Rôle :</strong> <?= htmlspecialchars($utilisateur->getRole()) ?></p>
        </div>
        <div class="profile-actions">
            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=update&controller=utilisateur&id=<?= $utilisateur->getId() ?>" class="btn btn-primary">Modifier mon profil</a>
            <a href="<?= \App\Meteo\Config\Conf::getBaseUrl(); ?>/Web/frontController.php?action=delete&controller=utilisateur&id=<?= $utilisateur->getId() ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">Supprimer mon compte</a>
        </div>
    <?php else: ?>
        <p>Aucune information de profil disponible.</p>
    <?php endif; ?>
</div>