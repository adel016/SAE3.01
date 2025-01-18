<h1>Changer le rôle de l'utilisateur</h1>
<form method="POST" action="/Web/frontController.php?action=changerRole&controller=utilisateur">
    <input type="hidden" name="id" value="<?= htmlspecialchars($utilisateur->getId()) ?>">
    <label for="role">Rôle :</label>
    <select id="role" name="role" required>
        <option value="utilisateur" <?= $utilisateur->getRole() === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
        <option value="admin" <?= $utilisateur->getRole() === 'admin' ? 'selected' : '' ?>>Admin</option>
    </select><br><br>
    <button type="submit">Changer le rôle</button>
</form>
