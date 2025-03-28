<?php

// Chemin du fichier de sauvegarde SQL
$backupFile = __DIR__ . '/../Sauvegardes/BD_SAE_METEOVISION.sql';

// Vérifie si le fichier existe
if (file_exists($backupFile)) {
    // Paramètres de téléchargement
    header('Content-Description: File Transfer');
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backupFile));

    // Lire et envoyer le fichier
    readfile($backupFile);
    exit;
} else {
    echo "Erreur : Le fichier de sauvegarde n'existe pas.";
}
