<?php
require_once __DIR__ . '/paths.php';

// Charger la config locale si elle existe (déploiement cPanel / prod)
$host     = 'localhost';
$dbname   = 'management';
$username = 'root';
$password = '';

$localConfig = __DIR__ . '/db.local.php';
if (file_exists($localConfig)) {
    require $localConfig;
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Pour les scripts (ex: backup) qui ont besoin des identifiants
$GLOBALS['db_credentials'] = ['host' => $host, 'dbname' => $dbname, 'user' => $username, 'password' => $password];

require_once __DIR__ . '/session.php';

function enregistrer_log($pdo, $message) {
    $user_id = $_SESSION['user_id'] ?? 1;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action, adresse_ip) VALUES (?, ?, NOW(), ?)");
    $stmt->execute([$user_id, $message, $ip]);
}
