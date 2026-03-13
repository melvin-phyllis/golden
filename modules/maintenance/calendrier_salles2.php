<?php
session_start();
require_once '../../config/db.php';

// Récupération des réservations de salles triées par date
$sql = "SELECT rs.*, s.nom_salle 
        FROM reservations_salles rs 
        JOIN salles s ON rs.salle_id = s.id 
        WHERE rs.date_reservation >= CURRENT_DATE 
        ORDER BY rs.date_reservation ASC, rs.heure_debut ASC";
$stmt = $pdo->query($sql);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planning des Salles | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        .status-badge { background: rgba(212, 175, 55, 0.1); color: #D4AF37; border: 1px solid #D4AF37; padding: 2px 8px; border-radius: 4px; font-size: 10px; }
    </style>
</head>
<body class="p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-serif gold-text uppercase tracking-[5px]">Planning des Salles Bemar</h1>
                <p class="text-zinc-500 text-sm">Gestion des événements et réceptions</p>
            </div>
            <a href="gestion_salles.php" class="gold-border px-6 py-2 rounded-full text-xs hover:bg-[#D4AF37] hover:text-black transition">Retour</a>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <?php if (empty($reservations)): ?>
                <div class="text-center py-20 gold-border rounded-3xl bg-zinc-900/30">
                    <p class="text-zinc-500 italic">Aucun événement prévu prochainement.</p>
                </div>
            <?php else: ?>
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[10px] uppercase tracking-widest text-zinc-500">
                            <th class="px-6 pb-2">Date & Heure</th>
                            <th class="px-6 pb-2">Salle / Événement</th>
                            <th class="px-6 pb-2">Client / Responsable</th>
                            <th class="px-6 pb-2">Montant</th>
                            <th class="px-6 pb-2 text-right">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                        <tr class="bg-zinc-900/50 gold-border rounded-xl">
                            <td class="px-6 py-4 rounded-l-xl border-y border-l border-zinc-800">
                                <span class="block font-bold"><?= date('d/m/Y', strtotime($res['date_reservation'])) ?></span>
                                <span class="text-xs text-zinc-500"><?= $res['heure_debut'] ?> - <?= $res['heure_fin'] ?></span>
                            </td>
                            <td class="px-6 py-4 border-y border-zinc-800">
                                <span class="gold-text font-serif text-lg"><?= $res['nom_salle'] ?></span>
                            </td>
                            <td class="px-6 py-4 border-y border-zinc-800">
                                <span class="block"><?= $res['nom_client'] ?></span>
                                <span class="text-[10px] text-zinc-500 uppercase"><?= $res['type_piece'] ?>: <?= $res['num_piece'] ?></span>
                            </td>
                            <td class="px-6 py-4 border-y border-zinc-800">
                                <span class="font-mono"><?= number_format($res['montant_total'], 0, ',', ' ') ?> FCFA</span>
                            </td>
                            <td class="px-6 py-4 rounded-r-xl border-y border-r border-zinc-800 text-right">
                                <span class="status-badge">Confirmé</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
</body>
</html>