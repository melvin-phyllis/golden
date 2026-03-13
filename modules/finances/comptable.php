<?php
session_start();
require_once '../../config/db.php';

// Protection d'accès
// 'if (!isset($_SESSION['user_id'])) { header("Location: ../../login.php"); exit(); }'

try {
    // 1. REVENUS BRUTS PAR ACTIVITÉ (Mois en cours)
    // Calcul pour les CHAMBRES (Table: reservations)
    $rev_chambres = $pdo->query("SELECT SUM(montant_total) FROM reservations WHERE MONTH(date_arrivee) = MONTH(CURRENT_DATE) AND YEAR(date_arrivee) = YEAR(CURRENT_DATE)")->fetchColumn() ?? 0;

    // Calcul pour les SALLES (Table: reservations_salles)
    $rev_salles = $pdo->query("SELECT SUM(montant_total) FROM reservations_salles WHERE MONTH(date_reservation) = MONTH(CURRENT_DATE) AND YEAR(date_reservation) = YEAR(CURRENT_DATE)")->fetchColumn() ?? 0;

    // Calcul pour RESTO & BAR (On prend les données de la table menu_items/commandes si remplies, sinon 0 pour l'instant)
    $rev_resto = 0; // À lier avec ta table commandes plus tard
    $rev_bar = 0;

    $revenu_brut_total = $rev_chambres + $rev_salles + $rev_resto + $rev_bar;

    // 2. RAPPORT DES DÉPENSES (Table: depenses)
    $total_depenses = $pdo->query("SELECT SUM(montant) FROM depenses WHERE MONTH(date_depense) = MONTH(CURRENT_DATE)")->fetchColumn() ?? 0;

    // 3. BÉNÉFICE BRUT & FLUX DE TRÉSORERIE
    $benefice_brut = $revenu_brut_total - $total_depenses;
    $flux_tresorerie = $revenu_brut_total - $total_depenses; // Trésorerie nette

} catch (PDOException $e) {
    die("Erreur comptable : " . $e->getMessage());
}


?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #050505; color: #a1a1aa; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .sidebar-item:hover { background: rgba(212, 175, 55, 0.1); color: #D4AF37; }
        .active-link { border-right: 3px solid #D4AF37; color: #D4AF37; background: rgba(212, 175, 55, 0.05); }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="w-64 border-r border-zinc-800 flex flex-col fixed h-full bg-[#050505] z-50">
        <div class="p-8">
            
            <h1 class="text-[#D4AF37] font-serif text-2xl tracking-tighter">AU BEMAR </h1>
            <p class="text-[9px] text-zinc-600 uppercase tracking-[0.4em] mt-1"><i>C'est rien que l'amour</i> </p>
        </div>

        <nav class="flex-1 mt-4 px-4 space-y-2">
            <a href="../admin/accueil.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-gauge-high w-5"></i>A La Accueil
            </a>


          <a href="../reception/dashboard.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-user-tie w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Réception
            </a>


            <a href="../conciergerie/conciergerie_dash.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-utensils w-5 transition-transform duration-300 group-hover:scale-110"></i>
             Restaurant & Bar
            </a>


             <a href="../maintenance/gestion_salles.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-champagne-glasses w-5 group-hover:scale-110 transition"></i>
                Salles d'Événement
            </a>


        
            <a href="../stock/stock_dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-boxes-stacked w-5"></i> Stocks
            </a>
            
        
        </nav>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="../admin/mon_profil.php"
            class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-chart-pie w-5"></i> Mon Profile
            
            </a>
                <a href="../../logout.php" class="flex items-center gap-4 px-4 py-3 text-red-900 hover:text-red-500 transition text-[10px] uppercase font-bold">
                <i class="fa-solid fa-power-off"></i> Déconnexion
            </a>
                <?php else: ?>
                    <a href="../../inscription.php" class="nav-link">Inscription</a>
                    <a href="../../login.php" class="nav-link border border-[#D4AF37] px-5 py-2 rounded-full text-[#D4AF37] hover:bg-[#D4AF37] hover:text-black transition">Se connecter</a>
                <?php endif; ?>
        </div>

        
    </aside>

    <main class="flex-1 ml-64">
        <header class="h-20 border-b border-zinc-800 flex items-center justify-between px-10 bg-[#050505]/80 backdrop-blur-md sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] uppercase tracking-widest font-bold text-zinc-500">Système Live</span>
            </div>
            
            <div class="flex items-center gap-6">
                <button class="text-zinc-500 hover:text-white"><i class="fa-regular fa-bell"></i></button>
                <div class="h-8 w-[1px] bg-zinc-800"></div>
                <div class="flex items-center gap-3">
                    <p class="text-xs font-bold text-white uppercase"><?= $_SESSION['nom'] ?? 'comptable' ?></p>
                    <div class="w-8 h-8 rounded-full bg-[#D4AF37] flex items-center justify-center text-black text-xs font-bold">
                        <?= substr($_SESSION['nom'] ?? 'C', 0, 1) ?>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="p-10">


<div class="space-y-10">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-white text-3xl font-serif tracking-tighter">Expertise Comptable <span class="text-[#D4AF37]">Bemar</span></h2>
            <p class="text-[10px] uppercase tracking-[0.4em] text-zinc-500 mt-2">Analyse consolidée par centre de profit</p>
        </div>
        <div class="flex gap-3">


         
          

            <a href="rapport_finance.php"
             class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
             <i class="fa-solid fa-clock-rotate-left w-5 transition-transform duration-300 group-hover:scale-110"></i>
             finances
            </a>

            



            <button class="bg-zinc-800 text-white text-[9px] px-4 py-2 rounded-lg uppercase font-bold hover:bg-[#D4AF37] hover:text-black transition">Exporter Excel</button>
            <button onclick="window.print()" class="glass-btn text-[9px] font-bold uppercase border border-zinc-800 px-6 py-3 rounded-xl hover:bg-white hover:text-black transition">
                <i class="fa-solid fa-print mr-2"></i> Imprimer Rapport
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-zinc-900/40 p-8 rounded-[2.5rem] border border-zinc-800">
            <p class="text-[10px] uppercase font-bold text-zinc-500 mb-4">Revenu Global Brut</p>
            <h3 class="text-4xl font-light text-white"><?= number_format($revenu_brut_total, 0, ',', ' ') ?> <span class="text-xs text-[#D4AF37]">CFA</span></h3>
        </div>
        <div class="bg-zinc-900/40 p-8 rounded-[2.5rem] border border-zinc-800">
            <p class="text-[10px] uppercase font-bold text-zinc-500 mb-4 text-red-400">Total Dépenses</p>
            <h3 class="text-4xl font-light text-white"><?= number_format($total_depenses, 0, ',', ' ') ?> <span class="text-xs text-red-400">CFA</span></h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-2xl">
            <p class="text-[10px] uppercase font-bold text-zinc-400 mb-4">Flux de Trésorerie</p>
            <h3 class="text-4xl font-black text-black"><?= number_format($flux_tresorerie, 0, ',', ' ') ?> <span class="text-xs">CFA</span></h3>
        </div>
    </div>

    <div class="bg-zinc-900/20 p-10 rounded-[3rem] border border-zinc-800">
        <h3 class="text-white text-xs font-bold uppercase mb-10 tracking-widest border-l-4 border-[#D4AF37] pl-4">Rentabilité par Activité</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="space-y-3">
                <div class="flex justify-between text-[10px] uppercase font-bold">
                    <span>Hébergement</span>
                    <span class="text-[#D4AF37]"><?= ($revenu_brut_total > 0) ? round(($rev_chambres/$revenu_brut_total)*100) : 0 ?>%</span>
                </div>
                <div class="h-2 w-full bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-[#D4AF37]" style="width: <?= ($revenu_brut_total > 0) ? ($rev_chambres/$revenu_brut_total)*100 : 0 ?>%"></div>
                </div>
                <p class="text-xl text-white font-mono"><?= number_format($rev_chambres, 0, ',', ' ') ?></p>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between text-[10px] uppercase font-bold">
                    <span>Location Salles</span>
                    <span class="text-blue-400"><?= ($revenu_brut_total > 0) ? round(($rev_salles/$revenu_brut_total)*100) : 0 ?>%</span>
                </div>
                <div class="h-2 w-full bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-400" style="width: <?= ($revenu_brut_total > 0) ? ($rev_salles/$revenu_brut_total)*100 : 0 ?>%"></div>
                </div>
                <p class="text-xl text-white font-mono"><?= number_format($rev_salles, 0, ',', ' ') ?></p>
            </div>
            
            <div class="space-y-3 opacity-50">
                <div class="flex justify-between text-[10px] uppercase font-bold text-zinc-500">
                    <span>Restaurant</span>
                    <span>0%</span>
                </div>
                <div class="h-2 w-full bg-zinc-800 rounded-full"></div>
                <p class="text-xl font-mono">0</p>
            </div>

            <div class="space-y-3 opacity-50">
                <div class="flex justify-between text-[10px] uppercase font-bold text-zinc-500">
                    <span>Bar</span>
                    <span>0%</span>
                </div>
                <div class="h-2 w-full bg-zinc-800 rounded-full"></div>
                <p class="text-xl font-mono">0</p>
            </div>
        </div>
    </div>
</div>
<?php include 'compta_details.php'; ?>

<?php include 'archivage.php'; ?>

<?php include '../layout/footer.php'; ?>



    
</body>
</html>