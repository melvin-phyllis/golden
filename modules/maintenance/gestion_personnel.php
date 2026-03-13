<?php
session_start();
require_once '../../config/db.php';


// // Sécurité : Redirection si non connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit();
// }

// 1. Récupération de la date du jour
$date_aujourdhui = date('Y-m-d');

// 2. La requête SQL corrigée avec les NOUVEAUX noms de tables
// On lie equipe_prestige (p) avec equipe_presences (pr)
$sql = "SELECT p.*, pr.heure_arrivee, pr.heure_depart, pr.statut_presence, pr.id as presence_id 
        FROM equipe_prestige p 
        LEFT JOIN equipe_presences pr ON p.id = pr.employe_id AND pr.date_jour = ?
        ORDER BY p.fonction ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$date_aujourdhui]);
$equipe = $stmt->fetchAll();



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supervision Équipe | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap');
        body { background: #050505; color: #fff; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .status-present { color: #22c55e; font-weight: bold; }
        .status-absent { color: #ef4444; }

         .font-serif { font-family: 'Playfair Display', serif; }
          
         .active-link { border-right: 3px solid #D4AF37; color: #D4AF37; background: rgba(212, 175, 55, 0.05); }

        
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
<body class="flex min-h-screen p-8">



<!-- SIDEBAR WOW VERSION -->
<aside class="w-64 h-screen bg-gradient-to-b from-black via-zinc-900 to-black border-r border-[#D4AF37] p-6 flex flex-col">

    <!-- LOGO -->
    <div class="mb-10 text-center">
        <h1 class="text-2xl font-serif text-[#D4AF37] tracking-widest">PRESTIGE</h1>
        <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Executive Panel</p>
    </div>

    <!-- MENU -->
    <nav class="space-y-3 flex-1">

        <a href="revenu_mensuel.php"
           class="sidebar-item active group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-coins w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Superviseur
        </a>



      <a href="../admin/accueil.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
            <i class="fa-solid fa-clock-rotate-left w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Accueil 
        </a>


       

        <a href="historique_presences.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
            <i class="fa-solid fa-chart-line w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Bilan Mensuel
        </a>

        <a href="maj_statut_panne.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
            <i class="fa-solid fa-screwdriver-wrench w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Suivi Maintenances
        </a>

        

        <a href="../reception/gouvernance.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-landmark w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Gouvernance
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

</aside>


    <div class="max-w-6xl mx-auto">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-widest">Registre du Personnel</h1>
                <p class="text-zinc-500 text-xs">Aujourd'hui : <?= date('d/m/Y') ?></p>
            </div>
            <div class="flex gap-4">
                <a href="liste_pannes.php" class="bg-zinc-800 border border-zinc-700 px-4 py-2 rounded-xl text-[10px] uppercase font-bold hover:border-[#D4AF37]">Liste pannes</a>


              <a href="ajouter_personnel.php" class="bg-zinc-800 border border-zinc-700 px-4 py-2 rounded-xl text-[10px] uppercase font-bold hover:border-[#D4AF37]">Recruter</a>


                <a href="../maintenance/portail_equipe.php" class="bg-[#D4AF37] text-black px-4 py-2 rounded-xl text-[10px] font-bold uppercase">Retour POS</a>
            </div>
        </header>

        <div class="bg-zinc-900/50 rounded-3xl overflow-hidden gold-border">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-800 text-[#D4AF37] uppercase text-[10px]">
                    <tr>
                        <th class="p-5">Collaborateur</th>
                        <th class="p-5">Contact</th>
                        <th class="p-5">Présence</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    <?php foreach($equipe as $perso): ?>



                     <tr class="<?= ($perso['statut_presence'] == 'Absent') ? 'bg-red-900/10' : '' ?>">


                <tr class="hover:bg-zinc-800/30 transition border-b border-zinc-800/50">
               <td class="p-4">
                   <p class="font-bold uppercase text-sm"><?= $perso['nom_complet'] ?></p>
                  <p class="text-[9px] text-zinc-500 uppercase"><?= $perso['fonction'] ?></p>
               </td>

                <td class="p-4 font-mono text-xs text-[#D4AF37]">
                  <?= $perso['telephone'] ?>
                </td>

    <td class="p-4">
        <form action="pointage.php" method="POST" class="m-0">
            <input type="hidden" name="employe_id" value="<?= $perso['id'] ?>">
            <select name="statut" onchange="this.form.submit()" 
                    class="bg-black border border-zinc-800 text-[10px] p-2 rounded-lg outline-none focus:border-[#D4AF37] cursor-pointer">
                <option value="Absent" <?= ($perso['statut_presence'] == 'Absent') ? 'selected' : '' ?>>🔴 ABSENT</option>
                <option value="Présent" <?= ($perso['statut_presence'] == 'Présent') ? 'selected' : '' ?>>🟢 PRÉSENT</option>
                <option value="Repos" <?= ($perso['statut_presence'] == 'Repos') ? 'selected' : '' ?>>🟡 REPOS</option>
            </select>
        </form>
    </td>

    <td class="p-4 text-right">
        <div class="flex items-center justify-end gap-3">
            <a href="historique_presences.php?id=<?= $perso['id'] ?>" 
               class="bg-zinc-800 px-3 py-1.5 rounded text-[9px] uppercase font-bold hover:bg-zinc-700 transition">
               Heures
            </a>

            <a href="modifier_personnel.php?id=<?= $perso['id'] ?>" 
               class="text-zinc-500 hover:text-[#D4AF37] text-[10px] font-bold uppercase tracking-tighter transition">
               Modifier
            </a>

                    <a href="supprimer_personnel.php?id=<?= $perso['id'] ?>" 
                      onclick="return confirm('Supprimer définitivement ce collaborateur ?')"
                      class="text-red-900 hover:text-red-500 text-[10px] font-bolduppercase         tracking-tighter transition">
                      Supprimer
                   </a>
                 </div>
                    </td>
                </tr>


                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>



    <?php include '../layout/footer.php'; ?>
</body>
</html>