<?php
session_start();
require_once '../../config/db.php';

$res_id = $_GET['res_id'] ?? null;
if (!$res_id) { die("Accès refusé."); }

// Récupération des données pour la facture
$query = "SELECT r.*, c.nom_complet, c.telephone, ch.numero_chambre, t.tarif_nuit, t.libelle 
          FROM reservations r
          JOIN clients c ON r.client_id = c.id
          JOIN chambres ch ON r.chambre_id = ch.id
          JOIN types_chambre t ON ch.type_id = t.id
          WHERE r.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$res_id]);
$res = $stmt->fetch();

// Calculs
$start = new DateTime($res['date_arrivee']);
$end = new DateTime($res['date_depart']);
$nuits = $start->diff($end)->days ?: 1;
$total_chambre = $nuits * $res['tarif_nuit'];

// On récupère les extras passés en URL ou on met 0
$extras = $_GET['extras'] ?? 0;
$total_general = $total_chambre + $extras;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture_<?= $res['nom_complet'] ?>_#<?= $res_id ?></title>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 40px; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; }
        .hotel-info h1 { margin: 0; color: #D4AF37; letter-spacing: 2px; }
        .invoice-details { text-align: right; }
        .section-title { background: #f9f9f9; padding: 10px; font-weight: bold; text-transform: uppercase; font-size: 12px; margin-top: 30px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 12px; border-bottom: 1px solid #eee; color: #888; font-size: 11px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        
        .total-box { margin-top: 30px; margin-left: auto; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; }
        .grand-total { border-top: 2px solid #D4AF37; font-size: 18px; font-weight: bold; margin-top: 10px; }
        
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #D4AF37; border: none; color: white; border-radius: 5px;">Imprimer la facture</button>
        <a href="reservation.php" style="margin-left: 10px; text-decoration: none; color: #666;">Retour au registre</a>
    </div>

    <div class="header">
        <div class="hotel-info">
            <h1>BEMAR PRESTIGE</h1>
            <p>Avenue des Palmiers, Abidjan<br>Tél: +225 07 03 11 36 52</p>
        </div>
        <div class="invoice-details">
            <h2>FACTURE</h2>
            <p>N° INV-<?= date('Y') ?>-<?= $res_id ?><br>Date: <?= date('d/m/Y') ?></p>
        </div>
    </div>

    <div style="margin-top: 40px; display: flex; justify-content: space-between;">
        <div>
            <p style="color: #888; font-size: 11px; text-transform: uppercase; margin-bottom: 5px;">Facturé à</p>
            <strong><?= htmlspecialchars($res['nom_complet']) ?></strong>
            <p><?= htmlspecialchars($res['telephone']) ?></p>
        </div>
        <div style="text-align: right;">
            <p style="color: #888; font-size: 11px; text-transform: uppercase; margin-bottom: 5px;">Détails Chambre</p>
            <strong>Chambre #<?= $res['numero_chambre'] ?></strong>
            <p><?= $res['libelle'] ?></p>
        </div>
    </div>

    <div class="section-title">Détail des prestations Bémar</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Prix Unitaire</th>
                <th>Quantité</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Hébergement - Du <?= $start->format('d/m/Y') ?> au <?= $end->format('d/m/Y') ?></td>
                <td><?= number_format($res['tarif_nuit'], 0, ',', ' ') ?> FCFA</td>
                <td><?= $nuits ?> nuit(s)</td>
                <td style="text-align: right;"><?= number_format($total_chambre, 0, ',', ' ') ?> FCFA</td>
            </tr>
            <?php if($extras > 0): ?>
            <tr>
                <td>Frais supplémentaires (Consommations & Services)</td>
                <td><?= number_format($extras, 0, ',', ' ') ?> FCFA</td>
                <td>1</td>
                <td style="text-align: right;"><?= number_format($extras, 0, ',', ' ') ?> FCFA</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="total-box">
        <div class="total-row">
            <span>Sous-total</span>
            <span><?= number_format($total_general, 0, ',', ' ') ?> FCFA</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL À PAYER</span>
            <span style="color: #D4AF37;"><?= number_format($total_general, 0, ',', ' ') ?> FCFA</span>
        </div>
    </div>

    <div style="margin-top: 100px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 20px;">
        <p>Merci d'avoir choisi le Prestige Bémar. Nous espérons vous revoir. À très bientôt !</p>
    </div>

</body>
</html>