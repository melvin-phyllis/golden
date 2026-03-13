<?php
require_once '../../config/db.php';
session_start();

// Suppression d'un log précis
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM logs_activite WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

// Option : Tout supprimer (Vider le journal)
if (isset($_GET['action']) && $_GET['action'] == 'clear_all') {
    $pdo->query("TRUNCATE TABLE logs_activite");
}

// Retour à la page précédente
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();