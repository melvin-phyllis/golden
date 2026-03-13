<?php
session_start();
require_once '../../config/db.php';

// // Sécurité : Redirection si non connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit();
// }

// ACTION : Changer l'état de propreté
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    // On utilise 'Disponible' pour être raccord avec le Dashboard
    $nouvel_etat = ($_GET['action'] === 'clean') ? 'Disponible' : 'À nettoyer';
    
    $stmt = $pdo->prepare("UPDATE chambres SET statut = ? WHERE id = ?");
    $stmt->execute([$nouvel_etat, $id]);
    
    // Redirection vers la même page pour rafraîchir
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Récupération des chambres (On utilise 'Disponible' dans le tri aussi)
$chambres = $pdo->query("SELECT c.*, t.libelle FROM chambres c JOIN types_chambre t ON c.type_id = t.id ORDER BY FIELD(c.statut, 'À nettoyer', 'Occupée', 'Disponible'), c.numero_chambre ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gouvernance | Bémar Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .status-card { background: rgba(15, 15, 15, 0.6); backdrop-blur: 10px; transition: 0.4s; border-radius: 2rem; }
        .status-card:hover { transform: translateY(-5px); border-color: #D4AF37; }
        /* Couleurs d'états */
        .bg-cleaning { border-left: 4px solid #eab308; background: rgba(234, 179, 8, 0.05); }
        .bg-occupied { border-left: 4px solid #ef4444; background: rgba(239, 68, 68, 0.05); }
        .bg-free { border-left: 4px solid #22c15e; background: rgba(34, 197, 94, 0.05); }



        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .gold-gradient { background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%); }
        .nav-link { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; transition: 0.3s; }
        .nav-link:hover { color: #D4AF37; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        @keyframes fadeInDown {
         from { opacity: 0; transform: translateY(-10px); }
         to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down { animation: fadeInDown 0.5s ease-out; }

    </style>
</head>
<body class="p-10">



<nav class="border-b border-zinc-800 bg-black/50 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 gold-gradient rounded-lg flex items-center justify-center font-bold text-black text-xl shadow-[0_0_15px_rgba(212,175,55,0.3)]">B</div>
                <span class="font-serif tracking-widest text-lg hidden md:block">BEMAR <span class="text-[#D4AF37]">PRESTIGE</span></span>
            </div>

            <div class="flex items-center gap-8">
                <a href="../admin/dashboard.php" class="nav-link text-[#D4AF37]">Accueil</a>
                <a href="../maintenance/gestion_personnel.php" class="nav-link">Mon dash</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="../admin/mon_profil.php" class="nav-link">Mon Profil</a>
                    <a href="../../logout.php" class="nav-link bg-red-900/20 text-red-500 px-4 py-2 rounded-full border border-red-500/20">Déconnexion</a>
                <?php else: ?>
                    <a href="../../inscription.php" class="nav-link">Inscription</a>
                    <a href="../../login.php" class="nav-link border border-[#D4AF37] px-5 py-2 rounded-full text-[#D4AF37] hover:bg-[#D4AF37] hover:text-black transition">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>



    <div class="flex justify-between items-center mb-12">
        <div>
            <h1 class="text-4xl font-serif text-[#D4AF37] italic">Gouvernance</h1>
            <p class="text-zinc-500 text-xs uppercase tracking-[0.3em] mt-2">Gestion de la salubrité au Bémar    hôtel </p>
        </div>
        <a href="dashboard.php" class="text-zinc-400 hover:text-white text-xs uppercase tracking-widest border border-zinc-800 px-6 py-2 rounded-full transition">
            < i class="fa-solid fa-arrow-left mr-2"></i> Retour Dashboard
        </a>

        <a href="../maintenance/portail_equipe.php" class="bg-zinc-800 text-xs px-4 py-2 rounded-lg border border-zinc-700 hover:bg-[#D4AF37]">Retour ménage </a>
    </div>

    <div class="flex gap-6 mb-10 bg-zinc-900/30 p-4 rounded-2xl w-fit border border-zinc-800/50">
        <div class="flex items-center gap-2 text-[9px] uppercase font-bold text-green-500">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span> Prêt à la vente
        </div>
        <div class="flex items-center gap-2 text-[9px] uppercase font-bold text-yellow-500">
            <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span> Ménage requis
        </div>
        <div class="flex items-center gap-2 text-[9px] uppercase font-bold text-red-500">
            <span class="w-2 h-2 bg-red-500 rounded-full"></span> Occupé
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php foreach($chambres as $ch): 
            $statusClass = "";
            if($ch['statut'] == 'Disponible' || $ch['statut'] == 'Libre') $statusClass = "bg-free";
            elseif($ch['statut'] == 'Occupée') $statusClass = "bg-occupied";
            else $statusClass = "bg-cleaning";
        ?>
            <div class="status-card <?php echo $statusClass; ?> p-8 border border-zinc-800">
                <div class="flex justify-between items-start mb-6">
                    <span class="text-3xl font-serif italic text-white">#<?php echo $ch['numero_chambre']; ?></span>
                    <span class="text-[9px] bg-white/5 px-3 py-1 rounded-full text-zinc-400 uppercase tracking-widest"><?php echo $ch['libelle']; ?></span>
                </div>
                
                <div class="space-y-1 mb-8">
                    <p class="text-[9px] text-zinc-500 uppercase font-bold">État actuel</p>
                    <p class="text-sm font-medium">
                        <?php 
                        if($ch['statut'] == 'À nettoyer') echo '⏳ EN ATTENTE MÉNAGE';
                        elseif($ch['statut'] == 'Occupée') echo '👤 CLIENT EN CHAMBRE';
                        else echo '✨ PRÊTE / DISPONIBLE';
                        ?>
                    </p>
                </div>

                <?php if($ch['statut'] == 'À nettoyer'): ?>
                    <a href="?action=clean&id=<?php echo $ch['id']; ?>" 
                       class="block w-full text-center bg-[#D4AF37] text-black text-[10px] font-black py-4 rounded-xl uppercase hover:scale-105 transition shadow-lg shadow-[#D4AF37]/10">
                        Valider le Nettoyage
                    </a>
                <?php elseif($ch['statut'] == 'Disponible' || $ch['statut'] == 'Libre'): ?>
                    <a href="?action=dirty&id=<?php echo $ch['id']; ?>" 
                       class="block w-full text-center border border-zinc-800 text-zinc-500 text-[10px] py-4 rounded-xl uppercase hover:border-yellow-500/50 hover:text-yellow-500 transition">
                        Signaler Chambre Sale
                    </a>
                <?php else: ?>
                    <button disabled class="w-full text-center border border-zinc-900 text-zinc-700 text-[10px] py-4 rounded-xl uppercase cursor-not-allowed">
                        Occupée (Verrouillé)
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-20">
        <?php include '../layout/footer3.php'; ?>
    </div>
</body>
</html>