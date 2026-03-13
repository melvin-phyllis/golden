<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $role_id = $_POST['role_id'];
    $salaire = $_POST['salaire_base'];
    $planning = $_POST['planning_horaire'];
    $statut = $_POST['statut'];

    // 1. Mise à jour de l'utilisateur
    $sql = "UPDATE utilisateurs SET nom=?, role_id=?, salaire_base=?, planning_horaire=?, statut=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$nom, $role_id, $salaire, $planning, $statut, $id])) {
        
        // 2. ENREGISTREMENT DANS LES LOGS (Tracer l'action)
        $admin_name = $_SESSION['user_nom'] ?? 'Admin';
        $message = "L'administrateur a modifié la fiche de $nom (Salaire: $salaire CFA, Statut: $statut)";
        
        $log_sql = "INSERT INTO logs_activite (utilisateur_id, action, date_action) VALUES (?, ?, NOW())";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([$_SESSION['user_id'] ?? 1, $message]);

        header("Location: users_list.php?success=1");
    }
}



