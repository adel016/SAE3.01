<h1>Modifier l'utilisateur</h1>
<form method="POST" action="/Web/frontController.php?action=update&controller=utilisateur&id=<?= $utilisateur->getId() ?>">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($utilisateur->getNom()) ?>" required><br><br>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($utilisateur->getEmail()) ?>" required><br><br>

    <button type="submit">Modifier</button>
</form>
