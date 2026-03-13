<?php
require_once '../config/db.php';
$token = $_GET['token'] ?? '';

// Vérification de la validité du token
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Lien invalide ou expiré. Veuillez contacter l'administrateur.");
}
?>

<form action="update_password.php" method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
    <button type="submit">METTRE À JOUR</button>
</form>

<!-- La Page de Nouveau Mot de Passe -->
<!-- C'est ici que l'utilisateur définit son nouveau mot de passe sécurisé (bcrypt) -->