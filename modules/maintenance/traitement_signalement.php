<?php
require_once '../../config/db.php';

// On vérifie si le formulaire a bien été envoyé
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['chambre_id'])) {
    
    $chambre_id = $_POST['chambre_id'];
    $desc = $_POST['description'];
    $emp_id = $_POST['employe_id'];
    $nom_photo = null;

    // Gestion de l'image (si présente)
    if (isset($_FILES['photo_panne']) && $_FILES['photo_panne']['error'] == 0) {
        $extension = pathinfo($_FILES['photo_panne']['name'], PATHINFO_EXTENSION);
        $nom_photo = "panne_" . time() . "." . $extension;
        $destination = "../../uploads/maintenance/" . $nom_photo;
        
        if (!is_dir("../../uploads/maintenance/")) {
            mkdir("../../uploads/maintenance/", 0777, true);
        }
        
        move_uploaded_file($_FILES['photo_panne']['tmp_name'], $destination);
    }

    // Insertion sécurisée
    $stmt = $pdo->prepare("INSERT INTO maintenance (chambre_id, description_probleme, image_preuve, priorite, statut) VALUES (?, ?, ?, 'Moyenne', 'A faire')");
    $stmt->execute([$chambre_id, $desc, $nom_photo]);
    
    header("Location: portail_equipe.php?emp=" . $emp_id . "&success=1");
    exit();
} else {
    // Si on arrive ici sans données, on redirige simplement
    header("Location: portail_equipe.php");
    exit();
}