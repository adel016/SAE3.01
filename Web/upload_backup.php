<?php
// Vérifier si un fichier a été envoyé
if (isset($_FILES['backup-file']) && $_FILES['backup-file']['error'] == 0) {
    $uploadedFile = $_FILES['backup-file'];
    
    // Vérifier que c'est bien un fichier SQL
    if ($uploadedFile['type'] !== 'application/sql' && pathinfo($uploadedFile['name'], PATHINFO_EXTENSION) !== 'sql') {
        echo "Erreur : le fichier doit être un fichier SQL.";
        exit;
    }

    // Spécifier le chemin du dossier de sauvegarde
    $targetDir = __DIR__ . '/../Sauvegardes/'; // Le dossier de sauvegarde
    $existingFile = $targetDir . 'BD_SAE_METEOVISION.sql'; // Nom du fichier à remplacer

    // Vérifier si le fichier existant est là et le supprimer
    if (file_exists($existingFile)) {
        if (unlink($existingFile)) {
            echo "L'ancien fichier a été supprimé avec succès. ";
        } else {
            echo "Erreur lors de la suppression de l'ancien fichier.";
            exit;
        }
    } else {
        echo "Aucun fichier à supprimer. ";
    }

    // Spécifier le nouveau fichier à télécharger
    $newFilePath = $targetDir . 'BD_SAE_METEOVISION.sql'; // Nouveau fichier téléchargé

    // Déplacer le fichier téléchargé vers le dossier de sauvegarde
    if (move_uploaded_file($uploadedFile['tmp_name'], $newFilePath)) {
        // Message de succès
        echo "Le fichier " . htmlspecialchars($uploadedFile['name']) . " a été téléchargé avec succès.";
    } else {
        echo "Erreur lors de l'upload du fichier.";
    }
} else {
    echo "Aucun fichier n'a été envoyé ou erreur lors du téléchargement.";
}
?>
