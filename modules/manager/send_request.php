<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom_demande'];
    $email = $_POST['email_demande'];
    $poste = $_POST['poste_souhaite'];

    $stmt = $pdo->prepare("INSERT INTO demandes_acces (nom, email, poste) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$nom, $email, $poste])) {
        // Redirige vers le login avec un message de succès
        header("Location: login.php?request=sent");
    } else {
        header("Location: login.php?request=error");
    }
}






