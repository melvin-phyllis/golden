<?php
session_start();
require_once '../../config/db.php';

// On récupère les filtres si présents
$search = $_GET['search'] ?? '';

$query = "SELECT r.*, c.nom_complet, ch.numero_chambre, t.tarif_nuit, t.libelle 
          FROM reservations r
          JOIN clients c ON r.client_id = c.id
          JOIN chambres ch ON r.chambre_id = ch.id
          JOIN types_chambre t ON ch.type_id = t.id";

if (!empty($search)) {
    $query .= " WHERE c.nom_complet LIKE :search OR ch.numero_chambre LIKE :search";
}
$query .= " ORDER BY r.date_arrivee DESC";

$stmt = $pdo->prepare($query);
if (!empty($search)) { $stmt->bindValue(':search', '%' . $search . '%'); }
$stmt->execute();
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Export Registre - Golden Prestige</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #333; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #D4AF37; padding-bottom: 10px; }
        .logo { font-size: 22px; font-weight: bold; color: #D4AF37; letter-spacing: 3px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f5f5f5; border: 1px solid #ddd; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        td { border: 1px solid #ddd; padding: 10px; }
        
        .footer { margin-top: 30px; font-size: 10px; color: #777; text-align: right; }
        
        /* Bouton flottant pour lancer l'impression avant que le PDF ne se génère */
        .btn-print { 
            background: #D4AF37; color: white; padding: 10px 20px; 
            border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;
        }

        @media print {
            .btn-print { display: none; }
            body { padding: 0; }
            table { font-size: 10px; }
        }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print();">Télécharger / Imprimer le Registre (PDF)</button>

    <div class="header">
        <div class="logo">BEMAR PRESTIGE HOTEL</div>
        <div style="text-transform: uppercase; font-size: 14px; margin-top: 5px;">Registre Officiel des Séjours au Bémar</div>
        <div style="font-size: 10px; color: #666;">Généré le <?= date('d/m/Y à H:i') ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date Arrivée</th>
                <th>Client</th>
                <th>Identité</th>
                <th>Chambre</th>
                <th>Nuits</th>
                <th>Montant Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($reservations as $res): 
                $start = new DateTime($res['date_arrivee']);
                $end = new DateTime($res['date_depart']);
                $nuits = $start->diff($end)->days ?: 1;
            ?>
            <tr>
                <td><?= $start->format('d/m/Y') ?></td>
                <td><strong><?= htmlspecialchars($res['nom_complet']) ?></strong></td>
                <td><?= htmlspecialchars($res['type_piece']) ?> : <?= htmlspecialchars($res['num_piece']) ?></td>
                <td><?= htmlspecialchars($res['numero_chambre']) ?> (<?= htmlspecialchars($res['libelle']) ?>)</td>
                <td><?= $nuits ?></td>
                <td><?= number_format($nuits * $res['tarif_nuit'], 0, ',', ' ') ?> FCFA</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Document certifié conforme par le système Bémar hôtel Prestige.
    </div>

</body>
</html>