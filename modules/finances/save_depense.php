<?php
require_once '../../config/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titre = $_POST['sous_categorie']; // On utilise le type comme titre
    $categorie = $_POST['categorie'];
    $sous_categorie = $_POST['sous_categorie'];
    $montant = $_POST['montant'];
    $date_depense = $_POST['date_depense'];
    $cree_par = $_SESSION['user_id'] ?? 1;

    // Gestion du justificatif (Upload)
    $justificatif_path = null;
    if (isset($_FILES['justificatif']) && $_FILES['justificatif']['error'] == 0) {
        $upload_dir = ROOT_PATH . '/uploads/factures/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $filename = time() . '_' . basename($_FILES['justificatif']['name']);
        $justificatif_path = 'uploads/factures/' . $filename;
        move_uploaded_file($_FILES['justificatif']['tmp_name'], $upload_dir . $filename);
    }

    try {
        $sql = "INSERT INTO depenses (titre, categorie, sous_categorie, montant, date_depense, justificatif, cree_par) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titre, $categorie, $sous_categorie, $montant, $date_depense, $justificatif_path, $cree_par]);

        // Log de l'action
        $log = $pdo->prepare("INSERT INTO logs_activite (utilisateur_id, action, date_action) VALUES (?, ?, NOW())");
        $log->execute([$cree_par, "Enregistrement d'une dépense : $sous_categorie ($montant CFA)"]);

        header("Location: rapport_finance.php?success=1");
    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
}