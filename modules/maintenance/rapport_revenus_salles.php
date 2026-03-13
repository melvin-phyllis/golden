<?php
require_once '../../config/db.php';

// 1. Calcul du revenu total global
$total_global = $pdo->query("SELECT SUM(montant_total) FROM reservations_salles")->fetchColumn();

// 2. Statistiques par salle (Revenu, nombre de réservations)
$sql = "SELECT s.nom_salle, s.type_salle, 
               COUNT(r.id) as nb_reservations, 
               SUM(r.montant_total) as revenu_total,
               SUM(r.option_restauration) as total_restau,
               SUM(r.option_equipement) as total_equip
        FROM salles s
        LEFT JOIN reservations_salles r ON s.id = r.salle_id
        GROUP BY s.id
        ORDER BY revenu_total DESC";
$stats = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Revenus | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] text-white p-8">
    <div class="max-w-6xl mx-auto">
        
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">Analyse des Revenus</h1>
                <p class="text-zinc-500 text-xs mt-1 uppercase">Performance par salle et services annexes</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-zinc-500 uppercase">Chiffre d'Affaires Total</p>
                <p class="text-3xl font-bold text-[#D4AF37]"><?= number_format($total_global, 0, ',', ' ') ?> <span class="text-sm">CFA</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <?php foreach($stats as $s): ?>
                <div class="bg-zinc-900/50 p-6 rounded-[2rem] border border-zinc-800">
                    <h3 class="text-zinc-400 text-[10px] uppercase font-bold mb-1"><?= $s['nom_salle'] ?></h3>
                    <p class="text-xl font-bold mb-4"><?= number_format($s['revenu_total'], 0, ',', ' ') ?> CFA</p>
                    
                    <div class="flex gap-4 border-t border-zinc-800 pt-4">
                        <div class="text-center">
                            <p class="text-[8px] text-zinc-500 uppercase">Résas</p>
                            <p class="text-xs font-bold"><?= $s['nb_reservations'] ?></p>
                        </div>
                        <div class="text-center border-l border-zinc-800 pl-4">
                            <p class="text-[8px] text-zinc-500 uppercase">Restau.</p>
                            <p class="text-xs font-bold"><?= $s['total_restau'] ?></p>
                        </div>
                        <div class="text-center border-l border-zinc-800 pl-4">
                            <p class="text-[8px] text-zinc-500 uppercase">Matériel</p>
                            <p class="text-xs font-bold"><?= $s['total_equip'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-zinc-900/20 rounded-[2rem] border border-zinc-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-zinc-900 text-[10px] uppercase text-zinc-500">
                    <tr>
                        <th class="p-5">Type de Salle</th>
                        <th class="p-5">Nom de la Salle</th>
                        <th class="p-5">Taux d'occupation (Estimé)</th>
                        <th class="p-5 text-right">Contribution au CA</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach($stats as $s): 
                        $pourcentage = $total_global > 0 ? ($s['revenu_total'] / $total_global) * 100 : 0;
                    ?>
                    <tr class="border-t border-zinc-800">
                        <td class="p-5 text-zinc-500 italic"><?= $s['type_salle'] ?></td>
                        <td class="p-5 font-bold uppercase text-[#D4AF37]"><?= $s['nom_salle'] ?></td>
                        <td class="p-5">
                            <div class="w-full bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-[#D4AF37] h-full" style="width: <?= $pourcentage ?>%"></div>
                            </div>
                        </td>
                        <td class="p-5 text-right font-mono"><?= round($pourcentage, 1) ?> %</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            <a href="gestion_salles.php" class="text-zinc-600 text-[10px] uppercase font-bold hover:text-white transition">← Retour au menu principal</a>
        </div>
    </div>

    <?php include '../layout/footer.php'; ?>
</body>
</html>