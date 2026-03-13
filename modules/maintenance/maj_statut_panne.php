<?php
require_once '../../config/db.php';
if (isset($_POST['id'])) {
    $stmt = $pdo->prepare("UPDATE maintenance SET statut = 'Terminé', date_resolution = NOW() WHERE id = ?");
    $stmt->execute([$_POST['id']]);
}
header("Location: liste_pannes.php");
exit();