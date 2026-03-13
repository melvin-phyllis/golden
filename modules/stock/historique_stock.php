<?php
session_start();
require_once '../../config/db.php';

// Requête pour lier l'historique au nom de l'article présent dans menu_items
$sql = "SELECT s.*, m.nom_article 
        FROM stocks s 
        JOIN menu_items m ON s.article_id = m.id 
        ORDER BY s.date_mouvement DESC";

$mouvements = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique Stock | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .badge-entree { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }
        .badge-sortie { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">Registre des Mouvements</h1>
                <p class="text-zinc-500 text-xs mt-1">Traçabilité complète des entrées et sorties de cave</p>
            </div>
            <div class="flex gap-3">
                <a href="mouvements_stock.php" class="bg-zinc-900 border border-zinc-700 px-4 py-2 rounded-xl text-[10px] uppercase font-bold hover:border-[#D4AF37]">Mettre stock ajour </a>
                <a href="stock_dashboard.php" class="bg-[#D4AF37] text-black px-4 py-2 rounded-xl text-[10px] font-bold uppercase">Tableau de Bord</a>
            </div>
        </div>

        <div class="bg-zinc-900/50 rounded-3xl overflow-hidden gold-border shadow-2xl">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-800/80 text-[#D4AF37] uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="p-5">Date & Heure</th>
                        <th class="p-5">Article</th>
                        <th class="p-5">Type</th>
                        <th class="p-5 text-center">Qté</th>
                        <th class="p-5 text-right">Motif / Justification</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php if(empty($mouvements)): ?>
                        <tr><td colspan="5" class="p-10 text-center text-zinc-600 italic">Aucun mouvement enregistré.</td></tr>
                    <?php endif; ?>

                    <?php foreach($mouvements as $mv): ?>
                    <tr class="hover:bg-zinc-800/30 transition">
                        <td class="p-5 text-zinc-500 font-mono text-[11px]">
                            <?= date('d/m/Y H:i', strtotime($mv['date_mouvement'])) ?>
                        </td>
                        <td class="p-5 font-bold uppercase text-xs">
                            <?= $mv['nom_article'] ?>
                        </td>
                        <td class="p-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-bold uppercase <?= $mv['type_mouvement'] == 'Entree' ? 'badge-entree' : 'badge-sortie' ?>">
                                <?= $mv['type_mouvement'] ?>
                            </span>
                        </td>
                        <td class="p-5 text-center font-bold <?= $mv['type_mouvement'] == 'Entree' ? 'text-green-500' : 'text-red-500' ?>">
                            <?= $mv['type_mouvement'] == 'Entree' ? '+' : '-' ?> <?= $mv['quantite'] ?>
                        </td>
                        <td class="p-5 text-right text-zinc-400 italic text-xs">
                            <?= $mv['motif'] ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>