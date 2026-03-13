<?php
require_once '../../config/db.php';
session_start();

// // // Pour éviter qu'un petit malin n'accède
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
//     header('Location: ../../login.php');
//     exit("Accès refusé");
// }


// 1. Récupération des statistiques clés
$total_users = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$total_logs = $pdo->query("SELECT COUNT(*) FROM logs_activite")->fetchColumn();
$total_masse_salariale = $pdo->query("SELECT SUM(salaire_base) FROM utilisateurs")->fetchColumn();
$last_backup = "21/02/2026 10:00"; // Exemple statique, peut être dynamisé

// 2. Dernières activités pour l'aperçu rapide
$recent_logs = $pdo->query("SELECT l.*, u.nom FROM logs_activite l JOIN utilisateurs u ON l.utilisateur_id = u.id ORDER BY l.date_action DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration Prestige | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        .glass { background: rgba(18, 18, 18, 0.8); backdrop-filter: blur(10px); }
        .gold-gradient { background: linear-gradient(135deg, #D4AF37 0%, #F1D37E 100%); }
        .card-hover:hover { border-color: #D4AF37; transform: translateY(-5px); transition: all 0.3s ease; }

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


/* Pour que l'icône de cloche bouge de la cloche bouge */
        @keyframes swing {
  0%, 100% { transform: rotate(0deg); }
  20% { transform: rotate(15deg); }
  40% { transform: rotate(-10deg); }
  60% { transform: rotate(5deg); }
  80% { transform: rotate(-5deg); }
}
.animate-swing { animation: swing 2s infinite; }
    </style>
</head>



<body class="bg-[#050505] text-zinc-300 min-h-screen font-sans flex">
   

   <!-- SIDEBAR WOW -->
    <div class="w-64 sidebar p-6 flex flex-col">

     <div class="p-8 mb-10 text-center">
        <div class="w-12 h-12 gold-gradient rounded-xl flex items-center justify-center font-bold text-black text-2xl mx-auto shadow-lg mb-4">B</div>
        <h2 class="font-serif tracking-[4px] text-sm uppercase">Bemar <span class="text-[#D4AF37]">Prestige</span></h2>
        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Management Portal</p>
        </div>

           <nav class="space-y-3 flex-1">

            <a href="manager_dashboard.php"
               class="sidebar-item active group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
                <i class="fa-solid fa-chart-pie w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Dashboard
            </a>


           
            <a href="../admin/accueil.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-gauge-high w-5"></i>A La Accueil
            </a>



            

            <a href="../reception/dashboard.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-user-tie w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Réceptionniste
            </a>




             </a>
            <a href="../finances/rapport_finance.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-chart-pie w-5"></i> Finances
            </a>



            <a href="../reception/reservation.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-calendar-check w-5 group-hover:scale-110 transition"></i>
                Réservations
            </a>

            <a href="../reception/clients.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-id-card w-5 group-hover:scale-110 transition"></i>
                Fiches Clients
            </a>

            <a href="../conciergerie/pos_bar.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-utensils w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Restaurant
            </a>

            <a href="../maintenance/gestion_personnel.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-broom w-5 group-hover:scale-110 transition"></i>
                Équipe d'Étage
            </a>

            <a href="../maintenance/gestion_salles.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-champagne-glasses w-5 group-hover:scale-110 transition"></i>
                Salles d'Événement
            </a>

        </nav>






       
         <div class="p-6 border-t border-zinc-900 bg-zinc-900/20">
        <?php if(isset($_SESSION['user_id'])): ?>
            
            <a href="../admin/mon_profil.php" 
               class="group flex items-center gap-4 px-4 py-3 rounded-xl text-xs uppercase font-semibold tracking-widest text-zinc-400 hover:text-[#D4AF37] transition-all duration-300">
                <i class="fa-solid fa-shield-halved w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Profil
            </a>
            
            <a href="../../logout.php" 
               class="flex items-center gap-4 px-4 py-3 text-red-900 hover:text-red-500 transition text-[10px] uppercase font-bold">
                <i class="fa-solid fa-power-off w-5"></i> Déconnexion
            </a>

        <?php else: ?>
            <div class="space-y-4">
                <a href="../../inscription.php" class="block text-center text-[10px] uppercase font-bold text-zinc-500 hover:text-white transition">Inscription</a>
                <a href="../../login.php" class="block text-center border border-[#D4AF37] px-4 py-3 rounded-xl text-[#D4AF37] text-[10px] uppercase font-bold hover:bg-[#D4AF37] hover:text-black transition">
                    Se connecter
                </a>
            </div>
        <?php endif; ?>
    </div>

           


           
    </div>

        </div>
    </div>


    <div class="flex">
        <main class="flex-1 p-8">
            
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h1 class="text-4xl font-serif text-white uppercase tracking-tighter">Console <span class="text-[#D4AF37]">Admin</span></h1>
                    <p class="text-zinc-500 text-xs uppercase tracking-[0.3em]">Hôtel Bemar & Spa • Sécurité et RH</p>
                </div>


                <?php include '../manager/notifications_demandes.php'; ?>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-white text-sm font-bold"><?= $_SESSION['user_nom'] ?? 'Admin Principal' ?></p>
                        <p class="text-[10px] text-[#D4AF37] uppercase">Super Utilisateur</p>
                    </div>
                    <div class="h-12 w-12 rounded-full gold-gradient p-0.5">
                        <img src="https://ui-avatars.com/api/?name=Admin+Prestige&background=000&color=D4AF37" class="rounded-full">
                    </div>
                </div>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="glass p-6 rounded-[2rem] border border-zinc-800 card-hover">
                    <i class="fa-solid fa-users text-[#D4AF37] mb-4"></i>
                    <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest">Collaborateurs</p>
                    <p class="text-3xl text-white font-light"><?= $total_users ?></p>
                </div>
                <div class="glass p-6 rounded-[2rem] border border-zinc-800 card-hover">
                    <i class="fa-solid fa-money-bill-trend-up text-[#D4AF37] mb-4"></i>
                    <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest">Masse Salariale</p>
                    <p class="text-2xl text-white font-light"><?= number_format($total_masse_salariale, 0, ',', ' ') ?> <span class="text-xs">CFA</span></p>
                </div>
                <div class="glass p-6 rounded-[2rem] border border-zinc-800 card-hover">
                    <i class="fa-solid fa-shield-halved text-[#D4AF37] mb-4"></i>
                    <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest">Actions Tracées</p>
                    <p class="text-3xl text-white font-light"><?= $total_logs ?></p>
                </div>
                <div class="glass p-6 rounded-[2rem] border border-zinc-800 card-hover">
                    <i class="fa-solid fa-database text-[#D4AF37] mb-4"></i>
                    <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest">Dernière Sauvegarde</p>
                    <p class="text-sm text-white mt-3 font-mono"><?= $last_backup ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1 space-y-4">
                    <h3 class="text-xs font-bold uppercase text-zinc-500 mb-6 tracking-widest px-2">Actions Système</h3>
                    
                    <a href="../../inscription.php" class="flex items-center justify-between p-4 glass border border-zinc-800 rounded-2xl hover:bg-[#D4AF37] hover:text-black transition group">
                        <span class="text-sm font-bold uppercase">Recruter un agent</span>
                        <i class="fa-solid fa-plus opacity-0 group-hover:opacity-100"></i>
                    </a>
                    <a href="users_list.php" class="flex items-center justify-between p-4 glass border border-zinc-800 rounded-2xl hover:bg-white hover:text-black transition group">
                        <span class="text-sm font-bold uppercase">Gestion RH & Salaires</span>
                        <i class="fa-solid fa-user-gear opacity-0 group-hover:opacity-100"></i>
                    </a>
                    <a href="backup_manager.php" class="flex items-center justify-between p-4 glass border border-zinc-800 rounded-2xl hover:bg-blue-600 hover:text-white transition group">
                        <span class="text-sm font-bold uppercase">Sauvegarde SQL</span>
                        <i class="fa-solid fa-download opacity-0 group-hover:opacity-100"></i>
                    </a>
                    <a href="logs_activite.php" class="flex items-center justify-between p-4 glass border border-zinc-800 rounded-2xl hover:bg-zinc-700 transition group text-red-400">
                        <span class="text-sm font-bold uppercase">Journal de Sécurité</span>
                        <i class="fa-solid fa-eye opacity-0 group-hover:opacity-100"></i>
                    </a>
                </div>

                <div class="lg:col-span-2">
                    <div class="glass border border-zinc-800 rounded-[2.5rem] p-8 h-full">
                        <div class="flex justify-between items-center mb-8">
                            <h3 class="text-xs font-bold uppercase text-zinc-500 tracking-widest">Activité Récente</h3>
                            <span class="bg-red-500/10 text-red-500 text-[8px] px-2 py-1 rounded-full animate-pulse uppercase">Live Monitor</span>
                        </div>
                        

<div class="space-y-6">
    <?php foreach($recent_logs as $log): ?>
    <div class="flex gap-4 items-start border-b border-zinc-800/50 pb-4 group">
        <div class="h-2 w-2 rounded-full mt-1.5 bg-green-500"></div>
        <div class="flex-1">
            <p class="text-xs text-zinc-300">
                <span class="text-[#D4AF37] font-bold"><?= $log['nom'] ?></span> 
                <?= htmlspecialchars($log['action']) ?>
            </p>
            <p class="text-[9px] text-zinc-600 mt-1 uppercase font-mono">
                <?= date('H:i', strtotime($log['date_action'])) ?>
            </p>
        </div>
        <a href="delete_log.php?id=<?= $log['id'] ?>" 
           onclick="return confirm('Supprimer ce log ?')"
           class="opacity-0 group-hover:opacity-100 text-zinc-700 hover:text-red-500 transition">
           <i class="fa-solid fa-xmark text-xs"></i>
        </a>
    </div>
    <?php endforeach; ?>
</div>

                        
                        <div class="mt-8 text-center">
                            <a href="logs_activite.php" class="text-[10px] text-zinc-500 uppercase hover:text-[#D4AF37] transition font-bold">Voir tout l'historique →</a>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>


</div>

</div>
</div>






<?php include '../layout/footer.php'; ?>
</body>
</html>