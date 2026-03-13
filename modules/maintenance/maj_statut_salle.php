<?php
require_once '../../config/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['salle_id'];
    $statut = $_POST['nouveau_statut'];
    
    $stmt = $pdo->prepare("UPDATE salles SET statut = ? WHERE id = ?");
    $stmt->execute([$statut, $id]);
    
    header("Location: details_salle.php?id=" . $id);
}



// Pour que le changement de statut (Disponible / Maintenance) fonctionne,