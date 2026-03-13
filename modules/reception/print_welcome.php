<?php
session_start();
require_once '../../config/db.php';

$res_id = $_GET['id'] ?? null;
if (!$res_id) die("Réservation introuvable.");

// Récupération des infos complètes
$sql = "SELECT r.*, c.nom_complet, ch.numero_chambre, t.libelle 
        FROM reservations r 
        JOIN clients c ON r.client_id = c.id 
        JOIN chambres ch ON r.chambre_id = ch.id 
        JOIN types_chambre t ON ch.type_id = t.id
        WHERE r.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$res_id]);
$data = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Carte de Bienvenue - <?= $data['nom_complet'] ?></title>
    <style>
        body { font-family: 'Georgia', serif; color: #1a1a1a; background: white; margin: 0; padding: 20px; }
        .card { border: 2px solid #D4AF37; padding: 30px; width: 400px; margin: auto; position: relative; }
        .logo { text-align: center; font-size: 24px; font-weight: bold; color: #D4AF37; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 5px; }
        .welcome { text-align: center; font-style: italic; font-size: 18px; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; }
        .label { color: #888; text-transform: uppercase; font-size: 10px; }
        .value { font-weight: bold; }
        .footer { text-align: center; margin-top: 40px; font-size: 10px; color: #aaa; }
        .wifi { margin-top: 20px; padding: 10px; background: #fdfaf0; border-radius: 8px; text-align: center; border: 1px dashed #D4AF37; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .card { border: 1px solid #D4AF37; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="card">
        <div class="logo">Prestige Bémar</div>
        <div class="welcome">Bienvenue, <?= $data['nom_complet'] ?></div>

        <div class="info-row">
            <span class="label">Chambre</span>
            <span class="value">#<?= $data['numero_chambre'] ?> (<?= $data['libelle'] ?>)</span>
        </div>
        <div class="info-row">
            <span class="label">Arrivée</span>
            <span class="value"><?= date('d/m/Y', strtotime($data['date_arrivee'])) ?></span>
        </div>
        <div class="info-row">
            <span class="label">Départ prévu</span>
            <span class="value"><?= date('d/m/Y', strtotime($data['date_depart'])) ?></span>
        </div>

        <div class="wifi">
            <span class="label">Wi-Fi Haut Débit :</span><br>
            <span class="value">PRESTIGE_GUEST / Gold2026</span>
        </div>

        <div class="footer">
            Une expérience signée Bémar hôtel  Prestige.<br>
            Le service d'étage est disponible pour vous.
        </div>
    </div>

    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.print()">Réimprimer</button>
        <button onclick="window.close()">Fermer</button>
    </div>
  


    <div class="info-row" style="margin-top: 15px; border-top: 1px dashed #eee; padding-top: 10px;">
    <span class="label">Durée du séjour</span>
    <span class="value">
        <?php 
            $d1 = new DateTime($data['date_arrivee']);
            $d2 = new DateTime($data['date_depart']);
            echo $d1->diff($d2)->days; 
        ?> Nuit(s)
    </span>
</div>

<!-- c'est tout affaire normal malgree les rouges -->
<div class="info-row" style="background: #fdfaf0; padding: 10px; border-radius: 5px; margin-top: 10px;">
    <span class="label" style="color: #D4AF37;">Total Estimé</span>
    <span class="value" style="font-size: 18px; color: #000;">
        <?= number_format($data['montant_total'], 0, ',', ' ') ?> FCFA
    </span>
</div>


<?php include '../layout/footer.php'; ?>
</body>
</html>