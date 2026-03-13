<?php
session_start();
require_once '../../config/db.php';

// Sécurité : Vérifier si l'utilisateur est bien réceptionniste
// if ($_SESSION['user_role'] !== 'Réceptionniste') {
//     header('Location: ../../login.php');
//     exit();
// }

// Récupération des statistiques en temps réel
$stats_chambres = $pdo->query("SELECT statut, COUNT(*) as nb FROM chambres GROUP BY statut")->fetchAll();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réception | Prestige Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


     <style>
        body { background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #000 0%, #1a1a1a 100%); border-right: 1px solid #D4AF37; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.4); }
        .card-stat { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); transition: 0.3s; }
        .card-stat:hover { border-color: #D4AF37; transform: translateY(-5px); }
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


        .status-free { color: #10b981; } /* Vert émeraude */
        .status-occupied { color: #ef4444; } /* Rouge rubis */
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <div class="w-64 sidebar p-6 flex flex-col">

        <div class="mb-10 text-center">
            <h1 class="text-2xl font-serif text-[#D4AF37] tracking-widest">AU BEMAR</h1>
            <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Réception Privée</p>
        </div>

        <!-- MENU WOW LUXE -->
        <nav class="space-y-3 flex-1">

            <a href="dashboard.php"
               class="sidebar-item active group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
                <i class="fa-solid fa-gauge-high w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Dashboard
            </a>

            <a href="chambres.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-bed w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Chambres
            </a>

            <a href="reservation.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-calendar-check w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Réservation
            </a>

            <a href="clients.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-id-card w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Fiches Clients
            </a>

            <a href="../conciergerie/pos_bar.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-utensils w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Restaurant
            </a>

            <a href="gouvernance.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-broom w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Dame menage
            </a>

            <a href="../maintenance/gestion_salles.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-champagne-glasses w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Salles d'Événement
            </a>

        </nav>

        <div class="pt-6 border-t border-zinc-800">
            <p class="text-xs text-gray-500">Connecté en tant que :</p>
            <p class="text-sm font-bold text-[#D4AF37]"><?php echo $_SESSION['user_nom']; ?></p>
            <a href="../../logout.php" class="text-xs text-red-400 hover:underline">Déconnexion</a>
        </div>

        
    </div>

    <!-- MAIN -->
    <main class="flex-1 p-8 overflow-y-auto">

        <header class="flex justify-between items-center mb-10">
            <h2 class="text-3xl font-light italic">
                Bienvenue au <span class="text-[#D4AF37] font-normal">Prestige Bemar</span>
            </h2>



            <div class="p-6 border-t border-zinc-900 bg-zinc-900/20">
        <?php if(isset($_SESSION['user_id'])): ?>
            
            <a href="../admin/mon_profil.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-user-tie w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Mon Profile
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




            <a href="gestion_tarifs.php"
               class="bg-[#D4AF37] text-black px-6 py-2 rounded-full font-bold hover:scale-105 transition">
                + Optimisation des revenus
            </a>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <?php foreach($stats_chambres as $stat): ?>
            <div class="card-stat p-6 rounded-xl gold-border">
                <p class="text-gray-400 text-sm uppercase"><?php echo $stat['statut']; ?></p>
                <h3 class="text-3xl font-bold"><?php echo $stat['nb']; ?></h3>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="card-stat p-6 rounded-xl gold-border">
                <h3 class="text-xl mb-4 text-[#D4AF37]">Arrivées prévues (Check-in) </h3>
                <table class="w-full text-left text-sm">
                    <thead class="text-gray-500 uppercase text-[10px] border-b border-zinc-800">
                        <tr>
                            <th class="py-3">Client</th>
                            <th>Chambre</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900">
                        <tr>
                            <td class="py-4 font-semibold">M. Jean Dupont</td>
                            <td>Suite  101</td>
                            <td><span class="px-3 py-1 bg-green-900 text-green-300 rounded-full text-[10px]">Prêt</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-stat p-6 rounded-xl gold-border">
                <h3 class="text-xl mb-4 text-[#D4AF37]">Taux d'occupation </h3>
                <canvas id="occupancyChart" height="150"></canvas>
            </div>
        </div>
    </main>

    <script>
        const ctx = document.getElementById('occupancyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                datasets: [{
                    label: 'Taux d\'occupation (%)',
                    data: [65, 78, 82, 90, 95, 100, 85],
                    borderColor: '#D4AF37',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(212, 175, 55, 0.1)'
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { color: '#1a1a1a' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>



    <?php include '../layout/footer.php'; ?>
</body>
</html>