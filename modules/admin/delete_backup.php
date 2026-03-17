<?php
require_once __DIR__ . '/../../config/db.php';

if (isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $file_path = ROOT_PATH . '/backups/' . $filename;

    if (file_exists($file_path)) {
        unlink($file_path); // Supprime le fichier

        // On trace l'action dans les logs
        $msg = "A supprimé définitivement le fichier de sauvegarde : $filename";
        $log = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action) VALUES (?, ?, NOW())");
        $log->execute([$_SESSION['user_id'] ?? 1, $msg]);

        header("Location: backup_manager.php?deleted=1");
    } else {
        echo "Fichier introuvable.";
    }
}