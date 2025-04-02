<?php

// Répertoire des sauvegardes
$backupDir = __DIR__ . '/../Sauvegardes/';

// Récupère tous les fichiers SQL du dossier des sauvegardes
$backupFiles = glob($backupDir . '*.sql');

// Vérifie s'il y a des fichiers dans le dossier
if (count($backupFiles) > 0) {
    // Trie les fichiers par date de modification (d'abord le plus récent)
    usort($backupFiles, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });

    // Sélectionne le fichier le plus récent
    $latestBackupFile = $backupFiles[0];

    // Paramètres de téléchargement
    header('Content-Description: File Transfer');
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . basename($latestBackupFile) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($latestBackupFile));

    // Lire et envoyer le fichier
    readfile($latestBackupFile);
    exit;
} else {
    echo "Erreur : Aucun fichier de sauvegarde trouvé.";
} 