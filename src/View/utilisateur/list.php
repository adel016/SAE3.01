<div class="profile-container">
    <!-- Section Profil -->
    <section class="user-profile">
        <h1>Bienvenue sur votre profil, <?= htmlspecialchars($utilisateur->getPrenom()) ?> !</h1>

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
    </section>

    <!-- Section Meteothèque -->
    <section class="meteotheque">
        <h2>Votre Meteothèque</h2>
        <?php if (!empty($requetes)): ?>
            <ul class="meteotheque-list">
                <?php foreach ($requetes as $requete): ?>
                    <li class="meteotheque-item">
                        <strong>Nom de la collection :</strong> <?= htmlspecialchars($requete->getNomCollection()) ?><br>
                        <strong>Description :</strong> <?= htmlspecialchars($requete->getDescription()) ?><br>
                        <strong>Date :</strong> <?= htmlspecialchars($requete->getDateCreation()) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Aucune requête enregistrée dans votre Meteothèque.</p>
        <?php endif; ?>
    </section>
</div>
