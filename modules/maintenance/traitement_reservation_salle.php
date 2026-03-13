<?php
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Récupérer l'ID de la salle depuis l'URL (car ton interface utilise ?id=...)
    $salle_id = $_GET['id'] ?? $_POST['salle_id']; 
    
    // 2. Synchronisation des noms avec ton HTML
    $client = $_POST['nom_client']; 
    $date = $_POST['date_event']; // Vérifie que c'est bien 'date_event' dans ton <input>
    $h_debut = $_POST['heure_debut'];
    $h_fin = $_POST['heure_fin'];
    
    // Identification (CNI/Passeport) que l'on voit sur ta nouvelle capture
    $type_piece = $_POST['type_piece'] ?? 'CNI';
    $num_piece = $_POST['num_piece'] ?? '';

    // Options
    $opt_restau = isset($_POST['restauration']) ? 1 : 0; 
    $opt_equip = isset($_POST['equipements']) ? 1 : 0;

    try {
        $pdo->beginTransaction();

        // 3. Récupérer le tarif
        $stmt = $pdo->prepare("SELECT tarif_heure FROM salles WHERE id = ?");
        $stmt->execute([$salle_id]);
        $salle = $stmt->fetch();

        if (!$salle) { throw new Exception("Salle introuvable."); }

        // 4. Calcul de la durée
        $debut = new DateTime($h_debut);
        $fin = new DateTime($h_fin);
        $intervalle = $debut->diff($fin);
        $heures = $intervalle->h + ($intervalle->days * 24) + ($intervalle->i / 60);

        // 5. Calcul du montant
        $total = $heures * $salle['tarif_heure'];

        // Ajout des options luxe
        if ($opt_restau) { $total += ($total * 0.20); } 
        if ($opt_equip) { $total += 50000; } 

        // 6. Enregistrement (avec les nouvelles colonnes d'identification)
        $sql = "INSERT INTO reservations_salles (salle_id, nom_client, date_reservation, heure_debut, heure_fin, option_restauration, option_equipement, montant_total, type_piece, num_piece) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $pdo->prepare($sql)->execute([
            $salle_id, $client, $date, $h_debut, $h_fin, 
            $opt_restau, $opt_equip, $total, $type_piece, $num_piece
        ]);

        $pdo->commit();
        header("Location: calendrier_salles.php?success=1");
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        die("Erreur de calcul ou d'insertion : " . $e->getMessage());
    }
}