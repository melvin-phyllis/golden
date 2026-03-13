<?php
session_start();
require_once '../../config/db.php';



// 1. Calculs PHP
$total_charges = $pdo->query("SELECT SUM(montant) FROM depenses WHERE MONTH(date_depense) = MONTH(CURRENT_DATE) AND YEAR(date_depense) = YEAR(CURRENT_DATE)")->fetchColumn() ?? 0;
$total_revenus = $pdo->query("SELECT SUM(montant_total) FROM reservations")->fetchColumn() ?? 0; // Remplace montant_total par le bon nom si besoin
$profit = $total_revenus - $total_charges;

// Récupération du détail des sorties
$repartition = $pdo->query("SELECT sous_categorie, SUM(montant) as total FROM depenses GROUP BY sous_categorie")->fetchAll();


include '../layout/header.php'; // On appelle le haut
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Finance | Prestige Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#050505] text-zinc-400 font-sans min-h-screen p-8">








    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-[0.3em]">Finance Bemar</h1>
                <p class="text-[10px] uppercase tracking-widest text-zinc-500 mt-2">Analyse des revenus et charges d'exploitation</p>
            </div>



           
                <a href="../reception/print_report.php"
               class="bg-zinc-800 border border-zinc-700 px-4 py-2 rounded-xl text-[10px] uppercase font-bold hover:border-[#D4AF37]">
                <span class="block text-[#D4AF37] mb-2 group-hover:scale-110 transition duration-300">📊</span>
                <span class="text-sm font-medium">Imprimer Rapport</span>
            </a>
            


            <div class="text-right">
                <p class="text-xs font-bold text-white uppercase"><?= date('F Y') ?></p>
                <a href="ajouter_depense.php" class="inline-block mt-2 text-[10px] bg-[#D4AF37] text-black px-6 py-2 rounded-full font-bold uppercase hover:bg-yellow-600 transition">
                    + Enregistrer une charge
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-zinc-900/40 border border-zinc-800 p-8 rounded-[2rem]">
                <p class="text-[9px] uppercase font-bold tracking-widest mb-4">Revenus Bruts</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-light text-white"><?= number_format($total_revenus, 0, ',', ' ') ?></span>
                    <span class="text-[10px] text-zinc-500 uppercase">CFA</span>
                </div>
                <div class="mt-4 h-1 w-full bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 w-full"></div>
                </div>
            </div>

            <div class="bg-zinc-900/40 border border-zinc-800 p-8 rounded-[2rem]">
                <p class="text-[9px] uppercase font-bold tracking-widest mb-4 text-red-500">Charges Totales</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-light text-white"><?= number_format($total_charges, 0, ',', ' ') ?></span>
                    <span class="text-[10px] text-zinc-500 uppercase">CFA</span>
                </div>
                <div class="mt-4 h-1 w-full bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-red-500" style="width: <?= ($total_revenus > 0) ? ($total_charges/$total_revenus)*100 : 0 ?>%"></div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-2xl shadow-[#D4AF37]/5">
                <p class="text-[9px] uppercase font-bold tracking-widest mb-4 text-zinc-400">Bénéfice Net</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-bold text-black"><?= number_format($profit, 0, ',', ' ') ?></span>
                    <span class="text-[10px] text-zinc-400 uppercase">CFA</span>
                </div>
                <p class="text-[10px] mt-4 font-bold <?= $profit >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    <i class="fa-solid <?= $profit >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' ?> mr-1"></i>
                    Statut du mois : <?= $profit >= 0 ? 'Rentable' : 'Déficitaire' ?>
                </p>
            </div>
        </div>

        <div class="bg-zinc-900/20 border border-zinc-800/50 p-10 rounded-[3rem]">
            <h3 class="text-white text-xs font-bold uppercase mb-8 tracking-[0.2em] flex items-center">
                <span class="w-8 h-[1px] bg-[#D4AF37] mr-4"></span>
                Détails des sorties de caisse
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
    <?php foreach($repartition as $item): ?>
    <div class="flex justify-between items-center py-4 border-b border-zinc-800/50 group hover:border-red-500/30 transition">
        <div class="flex flex-col">
            <span class="text-sm text-zinc-400 group-hover:text-white transition"><?= $item['sous_categorie'] ?></span>
            <span class="text-[9px] text-zinc-600 uppercase">Dernière saisie</span>
        </div>
        
        <div class="flex items-center gap-6">
            <span class="font-mono text-xs text-white"><?= number_format($item['total'], 0, ',', ' ') ?> CFA</span>
            
            <a href="supprimer_depense.php?cat=<?= urlencode($item['sous_categorie']) ?>" 
               onclick="return confirm('Voulez-vous vraiment annuler toutes les saisies de cette catégorie pour ce mois ?')"
               class="text-zinc-700 hover:text-red-500 transition-colors text-xs">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if(empty($repartition)) echo "<p class='text-zinc-600 italic text-xs'>Aucune dépense enregistrée ce mois-ci.</p>"; ?>
</div>
        </div>
    </div>




    <!-- <h1 class="text-white">Mon Rapport Financier</h1> -->

<?php
include '../layout/footer3.php'; // On appelle le bas
?>

</body>
</html>