<?php
/**
 * Migrations SQL – utilise les identifiants de config/db.local.php.
 * À lancer en CLI : php migrate.php
 * Ne pas appeler depuis le navigateur (sécurité).
 */

if (php_sapi_name() !== 'cli') {
    die('Ce script doit être exécuté en ligne de commande : php migrate.php');
}

$configDir = __DIR__ . '/config';

$host     = 'localhost';
$dbname   = 'management';
$username = 'root';
$password = '';

$localConfig = $configDir . '/db.local.php';
if (file_exists($localConfig)) {
    require $localConfig;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    fwrite(STDERR, "Erreur de connexion : " . $e->getMessage() . "\n");
    exit(1);
}

$sqlFile = __DIR__ . '/install_tables.sql';
if (!is_readable($sqlFile)) {
    fwrite(STDERR, "Fichier introuvable : install_tables.sql\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
$sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    fn($s) => $s !== ''
);

$done = 0;
$errors = [];

foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if ($stmt === '') continue;
    try {
        $pdo->exec($stmt . ';');
        $done++;
    } catch (PDOException $e) {
        $errors[] = $e->getMessage();
    }
}

if (!empty($errors)) {
    fwrite(STDERR, "Erreurs :\n" . implode("\n", $errors) . "\n");
    exit(1);
}

echo "Migrations OK. {$done} requête(s) exécutée(s).\n";
