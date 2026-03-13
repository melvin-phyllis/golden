<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(32)); // Génération d'un token aléatoire "Wow"
    $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        // Stockage du token dans la DB
        $update = $pdo->prepare("UPDATE utilisateurs SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $update->execute([$token, $expires, $email]);

        // Dans un vrai hôtel, on enverrait un email ici. 
        // Pour votre logiciel local, nous affichons une simulation élégante.
        echo "<div style='background:#000; color:#D4AF37; padding:20px; font-family:serif; text-align:center;'>
                <h2>SIMULATION EMAIL PRIVÉ</h2>
                <p>Un lien de réinitialisation a été généré :</p>
                <a href='reset_password.php?token=$token' style='color:#fff;'>Réinitialiser mon mot de passe</a>
              </div>";
    } else {
        header('Location: forgot_password.php?error=notfound');
    }
}
?>

<!-- Le Script de Traitement -->
<!-- Ce script vérifie l'existence de l'utilisateur et génère un lien sécurisé -->