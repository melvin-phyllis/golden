<?php
require_once '../../config/db.php';

// // Sécurité : Redirection si non connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit();
// }


// CORRECTION : On utilise 'date_arrivee' qui est présent dans ta table
$colonne_date = 'date_arrivee'; 

try {
    // 1. CHIFFRE D'AFFAIRE (Basé sur montant_total que l'on voit sur ta photo)
    $ca = $pdo->query("SELECT SUM(montant_total) FROM reservations WHERE MONTH($colonne_date) = MONTH(CURRENT_DATE) AND YEAR($colonne_date) = YEAR(CURRENT_DATE)")->fetchColumn() ?? 0;

    
 // On vérifie le statut 'Occupée'
    $total_chambres = $pdo->query("SELECT COUNT(*) FROM chambres")->fetchColumn() ?: 1;
    // On vérifie le statut 'Occupée'
    $chambres_occupees = $pdo->query("SELECT COUNT(*) FROM chambres WHERE statut = 'Occupée'")->fetchColumn();
    $taux_occupation = round(($chambres_occupees / $total_chambres) * 100);

    // 3. RENTABILITÉ
    $total_depenses = $pdo->query("SELECT SUM(montant) FROM depenses WHERE MONTH(date_depense) = MONTH(CURRENT_DATE)")->fetchColumn() ?? 0;
    $rentabilite = $ca - $total_depenses;
    
    // AJOUTE CETTE LIGNE POUR SUPPRIMER L'ERREUR "UNDEFINED VARIABLE $MARGE"
    $marge = ($ca > 0) ? round(($rentabilite / $ca) * 100) : 0;

    // 4. DONNÉES POUR LE GRAPHIQUE
    $jours = []; $ventes = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $jours[] = date('d M', strtotime($date));
        $ventes[] = $pdo->query("SELECT SUM(montant_total) FROM reservations WHERE DATE($colonne_date) = '$date'")->fetchColumn() ?? 0;
    }
} catch (PDOException $e) {
    die("Erreur de calcul : " . $e->getMessage());
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Manager Luxury Dashboard | Prestige</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .gold-gradient {
            background: linear-gradient(135deg, #D4AF37 0%, #926F34 100%);
        }

        .sidebar {
            background: linear-gradient(180deg, #000 0%, #1a1a1a 100%);
            border-right: 1px solid #D4AF37;
        }

        .sidebar-item:hover {
            background: linear-gradient(90deg, rgba(212,175,55,0.15), transparent);
            color: #ffffff !important;
            transform: translateX(6px);
        }

        .sidebar-item.active {
            background: linear-gradient(90deg, rgba(212,175,55,0.25), transparent);
            color: #D4AF37 !important;
            box-shadow: 0 0 15px rgba(212,175,55,0.3);
        }
    </style>
</head>

<body class="bg-[#050505] text-zinc-300 min-h-screen font-sans flex">

    <!-- SIDEBAR WOW -->
    <div class="w-64 sidebar p-6 flex flex-col">

        <div class="mb-10 text-center">
            <h1 class="text-2xl font-serif text-[#D4AF37] tracking-widest">AU BEMAR</h1>
            <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Management Portal</p>
        </div>

        <nav class="space-y-3 flex-1">

            <a href="manager_dashboard.php"
               class="sidebar-item active group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
                <i class="fa-solid fa-chart-pie w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Dashboard
            </a>
  

            <a  href="../maintenance/accueil.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
            <i class="fa-solid fa-chart-line w-5 transition-transform duration-300 group-hover:scale-110"></i>
             Accueil 
       
            </a>


            <a href="../admin/admin_dash.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-shield-halved w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Admin
            </a>




            <a href="../reception/chambres.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-bed w-5 group-hover:scale-110 transition"></i>
                Chambres
            </a>

            

            <a href="../conciergerie/conciergerie_dash.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-bell-concierge w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Conciergerie
            </a>




            <a href="../maintenance/rapport_revenus_salles.php"
             class="sidebar-item active group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
             <i class="fa-solid fa-coins w-5 transition-transform duration-300 group-hover:scale-110"></i>
             Revenu Mensuel
            </a>






            <a href="../reception/dashboard.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-user-tie w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Réceptionniste
            </a>


            <a href="../admin/users_list.php"   class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
            <i class="fa-solid fa-users w-5"></i> Collaborateurs
            </a>




            <a href="../maintenance/gestion_personnel.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-broom w-5 group-hover:scale-110 transition"></i>
                Équipe d'Étage
            </a>

            <a href="../stock/stock_dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-boxes-stacked w-5"></i> Stocks
            </a>

        </nav>






        <div class="p-6 border-t border-zinc-900">
            <a href="../../logout.php" class="flex items-center gap-4 px-4 py-3 text-red-900 hover:text-red-500 transition text-[10px] uppercase font-bold">
                <i class="fa-solid fa-power-off"></i> Déconnexion
            </a>
        </div>

    </div>

    <!-- CONTENU PRINCIPAL -->
    <div class="flex-1 p-10">

        <div class="max-w-7xl mx-auto">

            <div class="flex justify-between items-end mb-10">
                <div>
                    <h1 class="text-4xl font-serif text-white tracking-tighter">
                        Executive <span class="text-[#D4AF37]">Overview</span>
                    </h1>
                    <p class="text-[10px] uppercase tracking-[0.5em] text-zinc-500">
                       Bemar Hôtel Prestige & Spa • Management Portal
                    </p>
                </div>

                <div class="glass px-6 py-3 rounded-2xl flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-[9px] uppercase text-zinc-500">Directeur Général</p>
                        <p class="text-xs font-bold text-white">Administration</p>
                    </div>
                    <a href="../admin/mon_profil.php"><div class="w-10 h-10 rounded-full gold-gradient flex items-center justify-center text-black shadow-lg">
                        <i class="fa-solid fa-user-tie"></i>
                    </div></a>
                </div>
            </div>

            <!-- Exemple carte -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="glass p-6 rounded-[2rem] relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 text-6xl text-white/5 group-hover:text-[#D4AF37]/10 transition">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <p class="text-[10px] uppercase font-bold text-zinc-500 mb-2">
                        Chiffre d'Affaire
                    </p>
                    <h2 class="text-3xl font-light text-white">
                        <?= number_format($ca, 0, ',', ' ') ?>
                        <span class="text-xs text-[#D4AF37]">CFA</span>
                    </h2>
                    <p class="text-[9px] mt-4 text-green-500">
                        <i class="fa-solid fa-chart-line"></i> +12% vs mois dernier
                    </p>

                </div>

            <div class="glass p-6 rounded-[2rem]">
                <p class="text-[10px] uppercase font-bold text-zinc-500 mb-2">Occupation</p>
                <h2 class="text-3xl font-light text-white"><?= $taux_occupation ?>%</h2>
                <div class="w-full h-1.5 bg-zinc-800 rounded-full mt-4">
                    <div class="h-full gold-gradient rounded-full" style="width: <?= $taux_occupation ?>%"></div>
                </div>
            </div>

            <div class="glass p-6 rounded-[2rem]">
                <p class="text-[10px] uppercase font-bold text-zinc-500 mb-2">Bénéfice Net</p>
                <h2 class="text-3xl font-light <?= $rentabilite >= 0 ? 'text-white' : 'text-red-500' ?>">
                    <?= number_format($rentabilite, 0, ',', ' ') ?> <span class="text-xs">CFA</span>
                </h2>
                <p class="text-[9px] mt-4 uppercase text-zinc-500 italic">Marge : <?= $marge ?>%</p>
            </div>

            <a href="#" class="gold-gradient p-6 rounded-[2rem] flex flex-col justify-center items-center text-black hover:scale-105 transition transform cursor-pointer">
                <i class="fa-solid fa-file-pdf text-2xl mb-2"></i>
                <span class="text-[10px] font-bold uppercase tracking-widest text-center">Mon doudou</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="glass p-8 rounded-[2.5rem]">
                <h3 class="text-white text-xs font-bold uppercase mb-6 tracking-widest">Performance Hebdomadaire (Revenus)</h3>
                <canvas id="revenueChart" height="200"></canvas>
            </div>
            
            <div class="glass p-8 rounded-[2.5rem]">
                <h3 class="text-white text-xs font-bold uppercase mb-6 tracking-widest">Répartition des Charges</h3>
                <canvas id="expenseChart" height="200"></canvas>
            </div>
        </div>
    </div>

 </div>

</div>
</div>




    <script>
        // Graphique des Revenus (Ligne)
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: <?= json_encode($jours) ?>,
                datasets: [{
                    label: 'Ventes CFA',
                    data: <?= json_encode($ventes) ?>,
                    borderColor: '#D4AF37',
                    backgroundColor: 'rgba(212, 175, 55, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }]
            },
            options: { 
                plugins: { legend: { display: false } },
                scales: { 
                    y: { grid: { color: '#1a1a1a' }, ticks: { color: '#555' } },
                    x: { grid: { display: false }, ticks: { color: '#555' } }
                }
            }
        });

        // Graphique des Charges (Doughnut)
        const ctxExp = document.getElementById('expenseChart').getContext('2d');
        new Chart(ctxExp, {
            type: 'doughnut',
            data: {
                labels: ['Fixes', 'Variables'],
                datasets: [{
                    data: [60, 40], // Exemple statique, peut être rendu dynamique
                    backgroundColor: ['#D4AF37', '#1a1a1a'],
                    borderColor: '#050505',
                    borderWidth: 5
                }]
            },
            options: { 
                plugins: { legend: { position: 'bottom', labels: { color: '#888', font: { size: 10, family: 'sans-serif' } } } },
                cutout: '80%'
            }
        });
    </script>









<?php include '../layout/footer.php'; ?>
</body>
</html>