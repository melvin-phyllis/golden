<?php
session_start();
require_once '../../config/db.php';

// 1. Récupération de l'ID de la salle via l'URL
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $pdo->beginTransaction();

        // 2. Optionnel : On vérifie si la salle a des réservations en cours
        // Pour éviter de supprimer une salle occupée, on peut simplement 
        // supprimer d'abord ses réservations (ou empêcher la suppression)
        $stmtDelRes = $pdo->prepare("DELETE FROM reservations_salles WHERE salle_id = ?");
        $stmtDelRes->execute([$id]);

        // 3. Suppression de la salle
        $stmt = $pdo->prepare("DELETE FROM salles WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit();
        
        // Redirection vers la page de gestion avec un message de succès
        header("Location: gestion_salles.php?msg=deleted");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header("Location: gestion_salles.php");
    exit();
}