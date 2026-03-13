<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configuration pour afficher les erreurs SQL avec élégance
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}




function enregistrer_log($pdo, $message) {
    // On récupère l'ID de la personne connectée (via la session)
    $user_id = $_SESSION['user_id'] ?? 1; // 1 par défaut pour les tests
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action, adresse_ip) VALUES (?, ?, NOW(), ?)");
    $stmt->execute([$user_id, $message, $ip]);
}
?>