<?php
session_start();
require_once '../../config/db.php';


// // Sécurité : Redirection si non connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit();
// }

// Récupération des produits avec calcul des alertes
// On considère que le seuil critique est de 5 unités par défaut
$sql = "SELECT *, (stock_actuel <= 5) as alerte FROM menu_items ORDER BY stock_actuel ASC";
$stocks = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Stock Premium | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>

     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



    <style>
        body { background: #050505; color: #fff; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .bg-alert { background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; }

     

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

<!-- LOGO -->
    <div class="text-center py-8 border-b border-zinc-800">
        
        <h1 class="text-2xl font-bold tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-yellow-600">
            AU BEMAR 
        </h1>

        <p class="text-[9px] text-zinc-600 uppercase tracking-[0.4em] mt-1"><i>C'est rien que l'amour</i></p>


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


            <a href="../conciergerie/conciergerie_dash.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-utensils w-5 transition-transform duration-300 group-hover:scale-110"></i>
             Restaurant & Bar
            </a>


            <a  href="historique_stock.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
            <i class="fa-solid fa-clock-rotate-left w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Historiques
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
        </div>



        

    </div>


    <div class="max-w-6xl mx-auto">

    
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">AVEC Bémar</h1><br>

             <p class="text-gray-500 italic">"ON EST AU TOP AVEC LES STOCKS "</p>

            <div class="space-x-4">
                <a href="mouvements_stock.php" class="bg-zinc-800 text-xs px-4 py-2 rounded-lg border border-zinc-700 hover:border-[#D4AF37]">Nouvel Arrivage / Sortie</a>


              <a href="../conciergerie/pos_bar.php" class="bg-zinc-800 text-xs px-4 py-2 rounded-lg border border-zinc-700 hover:bg-[#D4AF37]">Retour POS</a>
                
                
                

            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-zinc-900 p-6 rounded-3xl gold-border">
                <p class="text-zinc-500 text-[10px] uppercase">Valeur de la Cave</p>
                <p class="text-2xl font-serif text-[#D4AF37]">
                    <?php 
                        $valeur = array_reduce($stocks, function($a, $b){ return $a + ($b['prix_unitaire'] * $b['stock_actuel']); }, 0);
                        echo number_format($valeur, 0, ',', ' ');
                    ?> <small class="text-xs">FCFA</small>
                </p>
            </div>
            <div class="bg-zinc-900 p-6 rounded-3xl gold-border">
                <p class="text-zinc-500 text-[10px] uppercase">Ruptures imminentes</p>
                <p class="text-2xl font-serif text-red-500">
                    <?php echo count(array_filter($stocks, function($s){ return $s['stock_actuel'] <= 5; })); ?> Articles
                </p>
            </div>
        </div>

        <div class="bg-zinc-900 rounded-3xl overflow-hidden gold-border">
            <table class="w-full text-left">
                <thead class="bg-zinc-800 text-[#D4AF37] text-[10px] uppercase">
                    <tr>
                        <th class="p-4">Produit</th>
                        <th class="p-4">Catégorie</th>
                        <th class="p-4">Stock Actuel</th>
                        <th class="p-4">Statut</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach($stocks as $s): ?>
                    <tr class="<?= $s['alerte'] ? 'bg-red-500/5' : '' ?>">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="<?= $s['image_url'] ?>" class="w-10 h-10 rounded-lg object-cover">
                                <span class="font-bold"><?= $s['nom_article'] ?></span>
                            </div>
                        </td>
                        <td class="p-4 text-xs text-zinc-500"><?= $s['categorie'] ?></td>
                        <td class="p-4 font-mono <?= $s['alerte'] ? 'text-red-500' : 'text-green-500' ?>">
                            <?= $s['stock_actuel'] ?> unités
                        </td>
                        <td class="p-4">
                            <?php if($s['alerte']): ?>
                                <span class="bg-red-500 text-white text-[8px] px-2 py-1 rounded-full uppercase font-bold animate-pulse">Alerte Seuil</span>
                            <?php else: ?>
                                <span class="text-zinc-600 text-[8px] uppercase">Optimal</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <a href="mouvements_stock.php?id=<?= $s['id'] ?>" class="text-[10px] text-[#D4AF37] border-b border-[#D4AF37]">Ajuster</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>




</div>

</div>
</div>

</body>
</html>


<!-- Ce fichier permet de voir en un coup d'œil l'état de votre cave. -->