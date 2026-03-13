<?php
session_start();
require_once '../../config/db.php';

// 1. Définir le mois en cours
$mois_actuel = date('m');
$annee_actuelle = date('Y');
$nom_mois = date('F Y');

// 2. Calcul des revenus cumulés (Chambres)
$sqlCh = "SELECT SUM(montant_total) as total, COUNT(*) as nb FROM reservations 
          WHERE MONTH(date_arrivee) = ? AND YEAR(date_arrivee) = ?";
$stmtCh = $pdo->prepare($sqlCh);
$stmtCh->execute([$mois_actuel, $annee_actuelle]);
$resCh = $stmtCh->fetch();

// 3. Calcul des revenus cumulés (Salles)
$sqlSa = "SELECT SUM(montant_total) as total, COUNT(*) as nb FROM reservations_salles 
          WHERE MONTH(date_reservation) = ? AND YEAR(date_reservation) = ?";
$stmtSa = $pdo->prepare($sqlSa);
$stmtSa->execute([$mois_actuel, $annee_actuelle]);
$resSa = $stmtSa->fetch();

$grand_total = ($resCh['total'] ?? 0) + ($resSa['total'] ?? 0);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Mensuel - Bémar Prestige</title>
    <style>
        body { font-family: 'Arial', sans-serif; color: #333; background: #fff; margin: 0; padding: 40px; }
        .header { text-align: center; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; margin-bottom: 40px; }
        .logo { font-size: 28px; font-weight: bold; color: #D4AF37; text-transform: uppercase; letter-spacing: 5px; }
        .report-title { font-size: 18px; color: #666; margin-top: 10px; }
        
        .stats-grid { display: flex; justify-content: space-between; margin-bottom: 50px; }
        .stat-box { border: 1px solid #eee; padding: 20px; width: 30%; border-radius: 10px; }
        .stat-label { font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 10px; }
        .stat-value { font-size: 20px; font-weight: bold; color: #000; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f9f9f9; text-align: left; padding: 12px; border-bottom: 2px solid #eee; font-size: 12px; color: #999; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        
        .total-row { background: #fdfaf0; font-weight: bold; font-size: 18px; }
        .footer { text-align: center; margin-top: 100px; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 20px; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="header">
        <div class="logo">Bémar Prestige</div>
        <div class="report-title">Rapport d'Activité Mensuel : <?= $nom_mois ?></div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Hébergement (Chambres)</div>
            <div class="stat-value"><?= number_format($resCh['total'] ?? 0, 0, ',', ' ') ?> FCFA</div>
            <div style="font-size: 11px; color: #666;"><?= $resCh['nb'] ?> réservations</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Événementiel (Salles)</div>
            <div class="stat-value"><?= number_format($resSa['total'] ?? 0, 0, ',', ' ') ?> FCFA</div>
            <div style="font-size: 11px; color: #666;"><?= $resSa['nb'] ?> événements</div>
        </div>
        <div class="stat-box" style="border-color: #D4AF37;">
            <div class="stat-label" style="color: #D4AF37;">Chiffre d'Affaires Global</div>
            <div class="stat-value" style="font-size: 24px;"><?= number_format($grand_total, 0, ',', ' ') ?> FCFA</div>
        </div>
    </div>

    <h3>Détails des Revenus Bémar    hôtel </h3>
    <table>
        <thead>
            <tr>
                <th>Source</th>
                <th>Volume</th>
                <th>Montant Cumulé</th>
                <th>Part du CA</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Suites & Chambres standard</td>
                <td><?= $resCh['nb'] ?> nuits vendues</td>
                <td><?= number_format($resCh['total'] ?? 0, 0, ',', ' ') ?> FCFA</td>
                <td><?= $grand_total > 0 ? round(($resCh['total'] / $grand_total) * 100) : 0 ?>%</td>
            </tr>
            <tr>
                <td>Salles de Conférence & Bémar Party</td>
                <td><?= $resSa['nb'] ?> sessions</td>
                <td><?= number_format($resSa['total'] ?? 0, 0, ',', ' ') ?> FCFA</td>
                <td><?= $grand_total > 0 ? round(($resSa['total'] / $grand_total) * 100) : 0 ?>%</td>
            </tr>
            <tr class="total-row">
                <td colspan="2">TOTAL GÉNÉRAL</td>
                <td colspan="2"><?= number_format($grand_total, 0, ',', ' ') ?> FCFA</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Document confidentiel généré par le système Bémar hôtel  PMS le <?= date('d/m/Y à H:i') ?>.<br>
        Signature du Responsable Financier : ___________________________
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Lancer l'impression</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">Fermer</button>
    </div>


    
</body>
</html>