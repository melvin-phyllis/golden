<?php
require_once '../../config/db.php';
session_start();

if (isset($_GET['cat'])) {
    $sous_categorie = $_GET['cat'];
    $user_id = $_SESSION['user_id'] ?? 1;

    try {
        // 1. On récupère les infos avant de supprimer pour le log
        $check = $pdo->prepare("SELECT SUM(montant) FROM depenses WHERE sous_categorie = ? AND MONTH(date_depense) = MONTH(CURRENT_DATE)");
        $check->execute([$sous_categorie]);
        $montant_annule = $check->fetchColumn();

        // 2. Suppression des dépenses de cette catégorie pour le mois en cours
        $stmt = $pdo->prepare("DELETE FROM depenses WHERE sous_categorie = ? AND MONTH(date_depense) = MONTH(CURRENT_DATE)");
        $stmt->execute([$sous_categorie]);

        // 3. Journal d'activité (Log)
        $log = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action) VALUES (?, ?, NOW())");
        $log->execute([$user_id, "ANNULATION FINANCE : Suppression de la charge $sous_categorie ($montant_annule CFA)"]);

        header("Location: rapport_finance.php?msg=deleted");
    } catch (PDOException $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header("Location: rapport_finance.php");
}