<?php
require_once '../../config/db.php';
session_start();

// Configuration (A adapter selon ton serveur XAMPP)
$user = 'root';
$pass = ''; 
$db   = 'management'; // NOM DE TA BASE DE DONNEES
$date = date("Y-m-d_H-i-s");
$filename = "../../backups/prestige_db_$date.sql";

// Commande Windows pour mysqldump
$command = "C:\xampp\mysql\bin\mysqldump.exe --user=$user --password=$pass --host=localhost $db > $filename";

// Exécution de la commande
exec($command, $output, $return_var);

if ($return_var === 0) {
    // On enregistre l'action dans les LOGS
    $msg = "Sauvegarde manuelle du système effectuée avec succès.";
    $log = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action) VALUES (?, ?, NOW())");
    $log->execute([$_SESSION['user_id'] ?? 1, $msg]);

    header("Location: backup_manager.php?status=success");
} else {
    echo "Erreur lors de la sauvegarde. Vérifiez le chemin de mysqldump dans run_backup.php";
}