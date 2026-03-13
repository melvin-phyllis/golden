<?php
require_once '../../config/db.php';
session_start();

// Sécurité : Seul l'admin peut rejeter
if ($_SESSION['role'] !== 'Admin') { exit("Accès refusé"); }

if (isset($_GET['action']) && $_GET['action'] == 'rejeter' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // On supprime simplement la demande de la table temporaire
    $stmt = $pdo->prepare("DELETE FROM demandes_acces WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: dashboard_admin.php?msg=demande_rejete");
    exit();
}