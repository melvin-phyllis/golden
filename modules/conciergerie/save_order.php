<?php
session_start();
// Connexion à la base de données
require_once '../../config/db.php';

// On indique au navigateur qu'on répond en JSON
header('Content-Type: application/json');

// Récupération des données envoyées par le JavaScript (fetch)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data) {
    // 1. Préparation des variables
    // Si chambre_id est vide dans le menu déroulant, on met NULL
    $chambre_id = !empty($data['chambre_id']) ? $data['chambre_id'] : null;
    $total = isset($data['total']) ? $data['total'] : 0;
    $mode_paiement = isset($data['mode_paiement']) ? $data['mode_paiement'] : 'Espèces';
    
    // On récupère l'ID de l'utilisateur (concierge) connecté, sinon 1 par défaut
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;

    try {
        // On démarre une transaction pour être sûr que tout s'enregistre bien
        $pdo->beginTransaction();

        // 2. Insertion de la commande
// On utilise les variables définies plus haut ($chambre_id, $user_id, $total, $mode_paiement)
$stmt->execute([
    0,              // table_numero (0 car c'est une chambre)
    $chambre_id,    // ID de la chambre
    $user_id,       // ID de l'utilisateur (serveur/réception)
    $total,         // Total de la commande
    $mode_paiement  // 'Espèces', 'OM', 'Wave' ou 'En attente'
]);

// 3. Enregistrement du paiement
// On récupère l'ID de la commande qui vient d'être créée
$commande_id = $pdo->lastInsertId();

// ATTENTION : Ta table SQL s'appelle 'paiements' et la colonne est 'reservation_id'
$sqlPay = "INSERT INTO paiements (reservation_id, montant, mode_paiement, date_paiement) 
           VALUES (?, ?, ?, NOW())";

$stmtPay = $pdo->prepare($sqlPay);

// On lie l'ID de la commande à la colonne 'reservation_id' de la table paiements
$stmtPay->execute([
    $commande_id, 
    $total, 
    $mode_paiement
]);

// Validation de la transaction
$pdo->commit();

// Réponse pour le JavaScript
echo json_encode([
    'success' => true,
    'message' => 'Commande n°' . $commande_id . ' enregistrée et payée.'
]);

    } catch (PDOException $e) {
        // En cas d'erreur, on annule tout
        $pdo->rollBack();
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur SQL : ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Aucune donnée reçue.'
    ]);
}
?>