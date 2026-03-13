<?php
require_once '../../config/db.php';

$res_id = $_GET['id'] ?? null;
if (!$res_id) { 
    // Si pas d'ID, on prend la dernière réservation effectuée
    $res_id = $pdo->query("SELECT id FROM reservations_salles ORDER BY id DESC LIMIT 1")->fetchColumn();
}

$stmt = $pdo->prepare("SELECT r.*, s.nom_salle, s.tarif_heure 
                       FROM reservations_salles r 
                       JOIN salles s ON r.salle_id = s.id 
                       WHERE r.id = ?");
$stmt->execute([$res_id]);
$facture = $stmt->fetch();

if (!$facture) { echo "Facture introuvable"; exit(); }

// Calcul de la durée pour l'affichage
$debut = new DateTime($facture['heure_debut']);
$fin = new DateTime($facture['heure_fin']);
$duree = $debut->diff($fin)->h;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture_<?= $facture['nom_client'] ?>_Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; color: black; }
            .invoice-box { border: none; box-shadow: none; }
        }
    </style>
</head>
<body class="bg-zinc-100 p-10">

    <div class="max-w-3xl mx-auto bg-white p-12 shadow-xl rounded-sm invoice-box border-t-8 border-[#D4AF37]">
        <div class="flex justify-between items-start mb-16">
            <div>
                <h1 class="text-3xl font-serif font-bold text-zinc-900 uppercase tracking-tighter">Hôtel Prestige</h1>
                <p class="text-xs text-zinc-500 uppercase tracking-widest">Service Évènementiel</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-zinc-400 uppercase">Facture</h2>
                <p class="text-sm font-mono">#FAC-<?= date('Y') ?>-00<?= $facture['id'] ?></p>
                <p class="text-xs text-zinc-500 italic"><?= date('d/m/Y') ?></p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-10 mb-12">
            <div>
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase mb-2">Facturé à :</h3>
                <p class="text-lg font-bold text-zinc-800"><?= $facture['nom_client'] ?></p>
                <p class="text-sm text-zinc-500 italic">Évènement du <?= date('d/m/Y', strtotime($facture['date_reservation'])) ?></p>
            </div>
            <div class="text-right">
                <h3 class="text-[10px] font-bold text-zinc-400 uppercase mb-2">Espace réservé :</h3>
                <p class="text-lg font-bold text-zinc-800"><?= $facture['nom_salle'] ?></p>
                <p class="text-sm text-zinc-500 italic"><?= $facture['heure_debut'] ?> à <?= $facture['heure_fin'] ?> (<?= $duree ?>h)</p>
            </div>
        </div>

        <table class="w-full mb-12">
            <thead>
                <tr class="border-b-2 border-zinc-100 text-[10px] uppercase text-zinc-400">
                    <th class="py-4 text-left">Description</th>
                    <th class="py-4 text-center">Quantité/Base</th>
                    <th class="py-4 text-right">Total (CFA)</th>
                </tr>
            </thead>
            <tbody class="text-sm text-zinc-700">
                <tr class="border-b border-zinc-50">
                    <td class="py-6 font-medium">Location de salle (Tarif horaire)</td>
                    <td class="py-6 text-center"><?= $duree ?> Heure(s)</td>
                    <td class="py-6 text-right font-bold"><?= number_format($duree * $facture['tarif_heure'], 0, ',', ' ') ?></td>
                </tr>
                <?php if($facture['option_restauration']): ?>
                <tr class="border-b border-zinc-50">
                    <td class="py-6 font-medium">Service Restauration (Pause café/Déjeuner)</td>
                    <td class="py-6 text-center">Pack Premium</td>
                    <td class="py-6 text-right font-bold"><?= number_format(($duree * $facture['tarif_heure']) * 0.20, 0, ',', ' ') ?></td>
                </tr>
                <?php endif; ?>
                <?php if($facture['option_equipement']): ?>
                <tr class="border-b border-zinc-50">
                    <td class="py-6 font-medium">Location Équipements Son & Vidéo</td>
                    <td class="py-6 text-center">Forfait fixe</td>
                    <td class="py-6 text-right font-bold">50 000</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-1/2 bg-zinc-50 p-6 rounded-lg text-right">
                <p class="text-xs text-zinc-400 uppercase font-bold mb-1">Montant Total à Payer</p>
                <p class="text-3xl font-bold text-zinc-900"><?= number_format($facture['montant_total'], 0, ',', ' ') ?> <span class="text-sm">CFA</span></p>
            </div>
        </div>

        <div class="mt-20 border-t border-zinc-100 pt-8 text-center text-[9px] text-zinc-400 uppercase tracking-widest">
            Merci de votre confiance • Hôtel Prestige & Spa • Service Direction
        </div>
    </div>

    <div class="max-w-3xl mx-auto mt-8 flex justify-between no-print">
        <a href="gestion_salles.php" class="text-zinc-500 text-xs font-bold uppercase tracking-widest hover:text-black">← Retour</a>
        <button onclick="window.print()" class="bg-[#D4AF37] text-black px-8 py-3 rounded-full font-bold uppercase text-[10px] tracking-widest hover:bg-yellow-600 transition shadow-lg">
            Imprimer la facture
        </button>
    </div>
    

</body>
</html>