<?php
require_once __DIR__ . '/../../config/db.php';

$c = $GLOBALS['db_credentials'];
$backup_dir = ROOT_PATH . '/backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

$backup_file = $backup_dir . 'sauvegarde_' . date('Y-m-d_H-i-s') . '.sql';
$user = $c['user'];
$pass = $c['password'];
$host = $c['host'];
$dbname = $c['dbname'];
$file = str_replace('\\', '/', $backup_file);

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
    if (!file_exists($mysqldump)) {
        $mysqldump = 'mysqldump';
    }
    $command = sprintf('"%s" --user=%s --password=%s --host=%s %s > %s',
        $mysqldump, escapeshellarg($user), escapeshellarg($pass), escapeshellarg($host), escapeshellarg($dbname), escapeshellarg($file));
} else {
    $command = sprintf('mysqldump --user=%s --password=%s --host=%s %s > %s',
        escapeshellarg($user), escapeshellarg($pass), escapeshellarg($host), escapeshellarg($dbname), escapeshellarg($file));
}

exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "✅ Sauvegarde réussie ! Fichier : " . basename($backup_file);
} else {
    echo "❌ Erreur lors de la sauvegarde. Vérifiez que mysqldump est disponible sur le serveur.";
}
