<?php
require_once '../../config/db.php';

if (isset($_POST['chambre_id'])) {
    $stmt = $pdo->prepare("UPDATE chambres SET etat_menage = 'Propre' WHERE id = ?");
    $stmt->execute([$_POST['chambre_id']]);
    
    // Optionnel : On peut aussi créer un log ici pour l'historique
}

header("Location: portail_equipe.php?emp=" . ($_POST['employe_id'] ?? ''));
exit();