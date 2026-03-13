<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et protection des données
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $role_id = $_POST['role_id'];
    $salaire = $_POST['salaire_base'] ?? 0;
    $planning = htmlspecialchars($_POST['planning_horaire']);
    
    // Hachage du mot de passe (SÉCURITÉ)
    $pass_hache = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id, salaire_base, planning_horaire, statut) 
                VALUES (?, ?, ?, ?, ?, ?, 'Actif')";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $email, $pass_hache, $role_id, $salaire, $planning]);

        // Enregistrement dans l'historique (LOGS)
        $msg_log = "Création du compte pour le nouveau collaborateur : $nom ($email)";
        $log_stmt = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action) VALUES (?, ?, NOW())");
        $log_stmt->execute([$_SESSION['user_id'] ?? 1, $msg_log]);

        header("Location: users_list.php?msg=success");
        
    } catch (PDOException $e) {
        echo "Erreur lors de la création : " . $e->getMessage();
    }
}