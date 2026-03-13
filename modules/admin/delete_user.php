<?php
require_once '../../config/db.php';
session_start();

if (isset($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];

    // Empêcher l'admin de se supprimer lui-même
    if ($id_a_supprimer == $_SESSION['user_id']) {
        header("Location: users_list.php?error=self_delete");
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    if ($stmt->execute([$id_a_supprimer])) {
        // Enregistrer dans les logs
        $log = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action) VALUES (?, 'A supprimé définitivement un compte collaborateur', NOW())");
        $log->execute([$_SESSION['user_id']]);

        header("Location: users_list.php?success=deleted");
    }
}