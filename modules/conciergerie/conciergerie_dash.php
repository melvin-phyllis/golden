<?php
session_start();
require_once '../../config/db.php';

// // Sécurité : Redirection si non connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit();
// }

// ==========================
// CHIFFRE D'AFFAIRES DU JOUR
// ==========================
try {
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(total), 0) 
        FROM commandes 
        WHERE DATE(date_commande) = CURDATE()
    ");
    $stmt->execute();
    $total_ventes = $stmt->fetchColumn();
} catch (PDOException $e) {
    $total_ventes = 0;
}


// ==========================
// COMMANDES EN COURS
// ⚠️ IMPORTANT : adapte le nom de colonne ici
// ==========================

try {
    // ⚠️ Remplace 'statut' par le vrai nom de ta colonne
    $stmt2 = $pdo->prepare("
        SELECT COUNT(*) 
        FROM commandes 
        WHERE status = ?
    ");
    $stmt2->execute(['En préparation']);
    $commandes_actives = $stmt2->fetchColumn();
} catch (PDOException $e) {
    $commandes_actives = 0;
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Concierge | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-card { background: #111; border: 1px solid rgba(212, 175, 55, 0.2); transition: 0.3s; }
        .gold-card:hover { border-color: #D4AF37; transform: translateY(-5px); }



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
<body class="bg-[#050505] text-zinc-300 min-h-screen font-sans flex p-8">



 <!-- SIDEBAR WOW -->
    <div class="w-64 sidebar p-6 flex flex-col">

        <div class="mb-10 text-center">
            <h1 class="text-2xl font-serif text-[#D4AF37] tracking-widest">AU BEMAR </h1>
            <p class="text-[10px] text-gray-500 uppercase tracking-tighter"> <i>C'est rien que l'amour</i> </p>
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
            



            <a  href="liste_commandes.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
            <i class="fa-solid fa-clock-rotate-left w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Historiques
            </a>

            
            <a href="../maintenance/gestion_salles.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-champagne-glasses w-5 group-hover:scale-110 transition"></i>
                Salles d'Événement
            </a>



            <a href="../stock/stock_dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
            <i class="fa-solid fa-boxes-stacked w-5"></i> Stocks
            </a>

           

            <a href="../maintenance/gestion_personnel.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-broom w-5 group-hover:scale-110 transition"></i>
                Équipe d'Étage
            </a>




            
    <div class="p-6 border-t border-zinc-900 bg-zinc-900/20">
        <?php if(isset($_SESSION['user_id'])): ?>
            
            <a href="../admin/mon_profil.php" 
              class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-user-tie w-5 transition-transform duration-300 group-hover:scale-110"></i>
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


        </nav>

    </div>




    <div class="max-w-6xl mx-auto">
        <header class="flex justify-between items-center mb-12 border-b border-zinc-800 pb-6">
            <div>
                <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-[0.2em]"> une restauration continue</h1>
                <p class="text-gray-500 italic">Avec Gestion des points de vente et services(bar-restaurant) </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase">Chiffre d'Affaires Jour</p>
                <p class="text-xl font-bold text-green-500"><?php echo number_format($total_ventes, 0, ',', ' '); ?> FCFA</p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <a href="pos_bar.php" class="gold-card p-8 rounded-3xl text-center group">
                <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&w=500" class="w-full h-40 object-cover rounded-2xl mb-4 opacity-60 group-hover:opacity-100 transition">
                <h2 class="text-xl font-serif text-[#D4AF37]">Point de Vente (POS)</h2>
                <p class="text-xs text-zinc-500 mt-2">Bar & Restaurant</p>
            </a>

            <div class="gold-card p-8 rounded-3xl text-center">
                <div class="h-40 flex flex-col justify-center">
                    <p class="text-5xl font-serif text-[#D4AF37]"><?php echo $commandes_actives; ?></p>
                    <p class="text-xs uppercase tracking-widest text-gray-500 mt-2">Commandes en cours</p>
                </div>
            </div>

            <a href="menu_items.php" class="gold-card p-8 rounded-3xl text-center group">
                 <div class="h-40 flex items-center justify-center bg-zinc-900 rounded-2xl mb-4">
                    <svg class="w-12 h-12 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
                 </div>
                <h2 class="text-xl font-serif text-[#D4AF37]">Gestion Menu</h2>
                <p class="text-xs text-zinc-500 mt-2">Articles & Tarifs</p>
            </a>
        </div>
    </div>



</div>

</div>
</div>


<?php include '../layout/footer.php'; ?>
</body>
</html>