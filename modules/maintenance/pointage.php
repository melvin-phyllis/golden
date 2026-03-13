<?php
session_start();
require_once '../../config/db.php';

if (isset($_POST['employe_id']) && isset($_POST['statut'])) {
    $employe_id = $_POST['employe_id'];
    $statut = $_POST['statut'];
    $date_jour = date('Y-m-d');
    $heure_actuelle = date('H:i:s');

    // Vérifier si une présence existe déjà pour aujourd'hui
    $check = $pdo->prepare("SELECT id FROM equipe_presences WHERE employe_id = ? AND date_jour = ?");
    $check->execute([$employe_id, $date_jour]);
    $exist = $check->fetch();

    if ($exist) {
        // Mise à jour
        $sql = "UPDATE equipe_presences SET statut_presence = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$statut, $exist['id']]);
    } else {
        // Création (Insertion)
        // Si présent, on note l'heure d'arrivée automatiquement
        $heure_in = ($statut == 'Présent') ? $heure_actuelle : null;
        $sql = "INSERT INTO equipe_presences (employe_id, date_jour, statut_presence, heure_arrivee) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$employe_id, $date_jour, $statut, $heure_in]);
    }
}

header("Location: gestion_personnel.php");
exit();