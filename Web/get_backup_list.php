<?php

// Répertoire des sauvegardes
$backupDir = __DIR__ . '/../Sauvegardes/';

// Récupère tous les fichiers SQL du dossier des sauvegardes
$backupFiles = glob($backupDir . '*.sql');

// Si aucun fichier n'est trouvé
if (count($backupFiles) == 0) {
    echo "<tr><td colspan='3'>Aucune sauvegarde disponible.</td></tr>";
    exit;
}

// Trie les fichiers par date de modification (d'abord le plus récent)
usort($backupFiles, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

// Exclure le dernier fichier (celui qui est téléchargé)
$latestBackupFile = $backupFiles[0];
unset($backupFiles[0]);

// Afficher les autres fichiers
foreach ($backupFiles as $file) {
    $fileName = basename($file);
    $fileModificationDate = date('d/m/Y H:i:s', filemtime($file));

    echo "<tr>
            <td>$fileName</td>
            <td>$fileModificationDate</td>
            <td><a href='download_backup.php?file=$fileName' class='btn btn-primary'>Télécharger</a></td>
          </tr>";
}
?>
