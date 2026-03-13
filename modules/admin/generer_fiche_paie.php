<?php
require_once '../../config/db.php';
require_once '../../vendor/dompdf/autoload.inc.php'; 
session_start();

use Dompdf\Dompdf;
use Dompdf\Options;

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT u.*, r.nom_role FROM utilisateurs u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Calculs simples (Exemple : 10% de retenues sociales)
$salaire_brut = $user['salaire_base'];
$retenues = $salaire_brut * 0.10;
$salaire_net = $salaire_brut - $retenues;

$html = '
<style>
    body { font-family: sans-serif; color: #333; }
    .header { text-align: center; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; }
    .section { margin: 20px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px; border: 1px solid #eee; text-align: left; }
    .total-box { background: #f9f9f9; padding: 15px; margin-top: 20px; text-align: right; border: 1px solid #D4AF37; }
    .footer { font-size: 10px; color: #777; margin-top: 50px; text-align: center; }
</style>

<div class="header">
    <h2 style="color: #D4AF37;">HÔTEL PRESTIGE & SPA</h2>
    <p>Bulletin de Paie - Période de Février 2026</p>
</div>

<div class="section">
    <p><strong>Collaborateur :</strong> ' . $user['nom'] . '<br>
    <strong>Poste :</strong> ' . $user['nom_role'] . '<br>
    <strong>Email :</strong> ' . $user['email'] . '</p>
</div>

<table>
    <thead>
        <tr style="background: #eee;">
            <th>Désignation</th>
            <th>Base</th>
            <th>Taux / Retenue</th>
            <th>Montant (CFA)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Salaire de base</td>
            <td>' . number_format($salaire_brut, 0) . '</td>
            <td>-</td>
            <td>' . number_format($salaire_brut, 0) . '</td>
        </tr>
        <tr>
            <td>Cotisations sociales (CNPS)</td>
            <td>' . number_format($salaire_brut, 0) . '</td>
            <td>10%</td>
            <td style="color: red;">- ' . number_format($retenues, 0) . '</td>
        </tr>
    </tbody>
</table>

<div class="total-box">
    <h3 style="margin: 0;">NET À PAYER : ' . number_format($salaire_net, 0, ',', ' ') . ' CFA</h3>
</div>

<div class="footer">
    <p>Ce document est un bulletin de paie électronique généré par le système de gestion Prestige.<br>
    Hôtel Prestige & Spa • Cocody, Abidjan • Côte d\'Ivoire</p>
</div>';

$options = new Options();
$options->set('isHtml5ParserEnabled', false); 
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Fiche_Paie_" . $user['nom'] . ".pdf");