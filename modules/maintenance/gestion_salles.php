<?php
session_start();
require_once '../../config/db.php';

// // Sécurité : Redirection si non connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit();
// }

// Récupération des salles
$stmt = $pdo->query("SELECT * FROM salles ORDER BY id DESC");
$salles = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Salles | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-gradient { background: linear-gradient(145deg, #d4af37, #b8860b); }
        .salle-card { background: #111; border: 1px solid #222; transition: 0.3s; }
        .salle-card:hover { border-color: #d4af37; transform: translateY(-5px); }


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
<body class="bg-[#050505] text-zinc-300 min-h-screen font-sans flex p-6">



 <!-- SIDEBAR WOW -->
    <div class="w-64 sidebar p-6 flex flex-col">

        <div class="mb-10 text-center">
            <h1 class="text-2xl font-serif text-[#D4AF37] tracking-widest">AU BEMAR</h1>
            <p class="text-[10px] text-gray-500 uppercase tracking-tighter">L’excellence faire le meilleure </p>
        </div>

        <nav class="space-y-3 flex-1">

            <a href="../maintenance/dashboard.php"
               class="sidebar-item active group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
                <i class="fa-solid fa-chart-pie w-5 transition-transform duration-300 group-hover:scale-110"></i>
                Dashboard
            </a>


            <a href="../admin/accueil.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-gauge-high w-5"></i>A La Accueil
            </a>


            <a href="../maintenance/rapport_revenus_salles.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
            <i class="fa-solid fa-file-lines w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Rapport des revenus
            </a>

            <a  href="../maintenance/calendrier_salles2.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
            <i class="fa-solid fa-calendar-days w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Calendrier Réservations
            </a>

            <a href="../stock/stock_dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
            <i class="fa-solid fa-boxes-stacked w-5"></i> Stocks
            </a>

            

            <a href="gestion_personnel.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-broom w-5 group-hover:scale-110 transition"></i>
                Équipe d'Étage
            </a>

           
             <div class="p-6 border-t border-zinc-900 bg-zinc-900/20">
        <?php if(isset($_SESSION['user_id'])): ?>
            
            <a href="../admin/mon_profil.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-user-tie w-5 transition-transform duration-300 group-hover:scale-110"></i>
           Profile
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



        </nav>

    </div>



    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">Espaces Évènementiels</h1>
                <p class="text-zinc-500 text-xs mt-1 uppercase">Planification et Facturation des Salles</p>
            </div>
            <a href="ajouter_salle.php" class="bg-[#D4AF37] text-black font-bold px-6 py-3 rounded-xl text-xs uppercase tracking-widest hover:bg-yellow-600 transition">
            + Ajouter une Salle
           </a>
           <a href="miajout_salles.php" 
             class="bg-[#D4AF37] text-black px-6 py-3 rounded-xl font-bold hover:bg-white transition">
            <i class="fa fa-plus"></i> Mise ajout salles
           </a>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($salles as $s): ?>
                <div class="salle-card rounded-[2rem] overflow-hidden shadow-2xl">
                    <div class="h-52 relative overflow-hidden">
                        <img src="../../assets/img/salles/<?= $s['image_salle'] ?>" 
                             alt="<?= $s['nom_salle'] ?>" 
                             class="w-full h-full object-cover opacity-70">
                        <div class="absolute top-4 left-4">
                            <span class="bg-black/80 backdrop-blur-md text-[#D4AF37] text-[10px] px-3 py-1 rounded-full font-bold uppercase border border-[#D4AF37]/30">
                                <?= $s['type_salle'] ?>
                            </span>
                        </div>
                        <div class="absolute bottom-4 right-4">
                            <span class="text-[10px] uppercase font-bold px-3 py-1 rounded-lg <?= $s['statut'] == 'Disponible' ? 'bg-green-900/80 text-green-400' : 'bg-red-900/80 text-red-400' ?>">
                                ● <?= $s['statut'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold uppercase tracking-tighter"><?= $s['nom_salle'] ?></h2>
                            <div class="text-right">
                                <p class="text-[9px] text-zinc-500 uppercase">Capacité</p>
                                <p class="text-sm font-bold"><?= $s['capacite'] ?> Pers.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6 bg-zinc-900/50 p-4 rounded-2xl border border-zinc-800">
                            <div>
                                <p class="text-[8px] text-zinc-500 uppercase tracking-widest">Par Heure</p>
                                <p class="text-sm font-bold text-[#D4AF37]"><?= number_format($s['tarif_heure'], 0, ',', ' ') ?> <span class="text-[10px]">CFA</span></p>
                            </div>
                            <div class="border-l border-zinc-800 pl-4">
                                <p class="text-[8px] text-zinc-500 uppercase tracking-widest">Par Journée</p>
                                <p class="text-sm font-bold text-[#D4AF37]"><?= number_format($s['tarif_jour'], 0, ',', ' ') ?> <span class="text-[10px]">CFA</span></p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="reserver_salle.php?id=<?= $s['id'] ?>" 
                               class="flex-1 bg-white text-black text-center py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-zinc-200 transition">
                               Réserver
                            </a>
                            <a href="details_salle.php?id=<?= $s['id'] ?>" 
                               class="bg-zinc-800 text-white px-4 py-3 rounded-xl text-[10px] border border-zinc-700 hover:bg-zinc-700">
                               ⚙️
                            </a>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


</div>

</div>
</div>


<?php include '../layout/footer.php'; ?>
</body>
</html>