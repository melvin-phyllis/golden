<?php
require_once __DIR__ . '/../../config/db.php';

$c = $GLOBALS['db_credentials'];
$backup_dir = ROOT_PATH . '/backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

$date = date("Y-m-d_H-i-s");
$filename = $backup_dir . "prestige_db_{$date}.sql";

// Commande portable : Linux (cPanel) ou Windows (XAMPP)
$user = $c['user'];
$pass = $c['password'];
$host = $c['host'];
$db   = $c['dbname'];
$file = str_replace('\\', '/', $filename);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
    if (!file_exists($mysqldump)) {
        $mysqldump = 'mysqldump';
    }
    $command = sprintf('"%s" --user=%s --password=%s --host=%s %s > %s',
        $mysqldump, escapeshellarg($user), escapeshellarg($pass), escapeshellarg($host), escapeshellarg($db), escapeshellarg($file));
} else {
    $command = sprintf('mysqldump --user=%s --password=%s --host=%s %s > %s',
        escapeshellarg($user), escapeshellarg($pass), escapeshellarg($host), escapeshellarg($db), escapeshellarg($file));
}

exec($command, $output, $return_var);

if ($return_var === 0) {
    $msg = "Sauvegarde manuelle du système effectuée avec succès.";
    $log = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action) VALUES (?, ?, NOW())");
    $log->execute([$_SESSION['user_id'] ?? 1, $msg]);
    header("Location: backup_manager.php?status=success");
    exit;
}
echo "Erreur lors de la sauvegarde. Vérifiez que mysqldump est disponible sur le serveur (cPanel : généralement dans le PATH).";
