<?php
session_start();
// 1. Configuration du fuseau horaire (GMT pour la Côte d'Ivoire)
date_default_timezone_set('Africa/Abidjan');
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');

require_once '../../config/db.php';

// // Sécurité : Redirection si non connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit();
// }

try {
    // Statistiques Réservations
    $total_reservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE MONTH(date_arrivee) = MONTH(CURRENT_DATE) AND YEAR(date_arrivee) = YEAR(CURRENT_DATE)")->fetchColumn() ?: 0;
    $clients_du_jour = $pdo->query("SELECT COUNT(*) FROM reservations WHERE DATE(date_arrivee) = CURRENT_DATE")->fetchColumn() ?: 0;

    // Alerte Stock
    $alertes_stock = $pdo->query("SELECT COUNT(*) FROM stocks WHERE quantite_actuelle <= seuil_alerte")->fetchColumn() ?: 0;

    // Disponibilité Suites & Chambres
    $suites_data = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN statut IN ('Disponible', 'Libre') THEN 1 ELSE 0 END) as dispo FROM chambres ch JOIN types_chambre t ON ch.type_id = t.id WHERE t.libelle LIKE '%Suite%'")->fetch();
    $deluxe_data = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN statut IN ('Disponible', 'Libre') THEN 1 ELSE 0 END) as dispo FROM chambres ch JOIN types_chambre t ON ch.type_id = t.id WHERE t.libelle NOT LIKE '%Suite%'")->fetch();

    // Logs d'activités
    $activites = $pdo->query("SELECT l.*, u.nom FROM logs_activite l JOIN utilisateurs u ON l.utilisateur_id = u.id ORDER BY l.date_action DESC LIMIT 5")->fetchAll();

} catch (PDOException $e) {
    $total_reservations = $clients_du_jour = $alertes_stock = 0;
    $suites_data = $deluxe_data = ['total' => 0, 'dispo' => 0];
    $activites = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bémar Prestige | Dashboard Luxury</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #050505; color: #a1a1aa; overflow-x: hidden; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .sidebar-item { transition: 0.3s; color: #71717a; }
        .sidebar-item:hover { background: rgba(212, 175, 55, 0.1); color: #D4AF37; }
        .video-container { position: relative; height: 55vh; width: 100%; overflow: hidden; border-radius: 2.5rem; margin-bottom: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
        .video-container video { object-fit: cover; width: 100%; height: 100%; filter: brightness(0.6); }
        .video-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, transparent, #050505); }
    </style>
</head>
<body class="flex min-h-screen">

<div id="loader" class="fixed inset-0 z-[100] bg-[#050505] flex flex-col items-center justify-center transition-opacity duration-700">
    <div class="relative w-24 h-24">
        <div class="absolute inset-0 border-2 border-[#D4AF37]/10 rounded-full"></div>
        <div class="absolute inset-0 border-t-2 border-[#D4AF37] rounded-full animate-spin"></div>
        <div class="absolute inset-0 flex items-center justify-center font-serif text-[#D4AF37] text-2xl pulse">B</div>
    </div>
</div>

<aside class="w-64 border-r border-zinc-800 flex flex-col fixed h-full bg-[#050505] z-50">
    <div class="p-8">
        <span class="text-[#D4AF37] font-serif text-xl tracking-tighter uppercase block">Bémar Prestige</span>
        <span class="text-[8px] text-zinc-500 tracking-[0.3em] uppercase italic">C'est rien que l'amour</span>
    </div>

    <nav class="flex-1 px-4 space-y-1">
        <a href="baa.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl text-xs uppercase font-semibold tracking-widest text-[#D4AF37] bg-[#D4AF37]/5">
            <i class="fa-solid fa-gauge-high w-5"></i> Accueil
        </a>
        <a href="../reception/dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl text-xs uppercase font-semibold tracking-widest transition">
            <i class="fa-solid fa-user-tie w-5"></i> Réception
        </a>
        <a href="../reception/reservation.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl text-xs uppercase font-semibold tracking-widest transition">
            <i class="fa-solid fa-calendar-check w-5"></i> Réservation
        </a>
        <a href="../conciergerie/conciergerie_dash.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl text-xs uppercase font-semibold tracking-widest transition">
            <i class="fa-solid fa-utensils w-5"></i> Restaurant
        </a>
        <a href="../finances/comptable.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl text-xs uppercase font-semibold tracking-widest transition">
            <i class="fa-solid fa-chart-pie w-5"></i> Comptable
        </a>
        <a href="../stock/stock_dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl text-xs uppercase font-semibold tracking-widest transition">
            <i class="fa-solid fa-boxes-stacked w-5"></i> Stocks
        </a>


        <a href="../admin/admin_dash.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-shield-halved w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Admin
        </a>


        <a href="../maintenance/accueil.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-briefcase w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Manager
        </a>


        <a href="../reception/gouvernance.php"  class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-landmark w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Gouvernance
        </a>



    </nav>
</aside>

<main class="flex-1 ml-64">
    <header class="h-20 border-b border-zinc-800 flex items-center justify-between px-10 bg-[#050505]/80 backdrop-blur-md sticky top-0 z-40">
        <div class="flex items-center gap-4">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="text-[10px] uppercase tracking-widest font-bold text-zinc-500">Live Management System</span>
        </div>
        
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3 bg-zinc-900/50 p-1.5 rounded-full border border-zinc-800">
                <span class="text-[10px] font-bold text-white uppercase ml-3"><?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?></span>
                <a href="../../logout.php" class="bg-red-900/20 text-red-500 p-2 rounded-full hover:bg-red-500 transition">
                    <i class="fa-solid fa-power-off"></i>
                </a>
            </div>
        </div>
    </header>

    <div class="p-10">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-white text-4xl font-serif italic">Bienvenue au Bémar,</h2>
                <p class="text-zinc-500 text-sm mt-2 uppercase tracking-[0.2em]" id="current-date"><?= date('d F Y') ?></p>
            </div>
            
            <div class="flex gap-6 items-center bg-zinc-900/40 p-5 rounded-[2rem] border border-zinc-800/50 shadow-xl">
                <div class="text-right">
                    <p class="text-[18px] font-black text-white tracking-tighter" id="clock"><?= date('H:i:s') ?></p>
                    <p class="text-[9px] uppercase text-[#D4AF37] font-bold tracking-[0.2em]">Abidjan, CI</p>
                </div>
                <div class="h-10 w-[1px] bg-zinc-800"></div>
                <div class="flex flex-col items-center">
                    <i class="fa-solid fa-cloud-sun text-[#D4AF37] text-xl"></i>
                    <span class="text-white text-[10px] font-bold mt-1">29°C</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <div class="bg-gradient-to-br from-[#D4AF37] to-[#926F34] p-6 rounded-[2.5rem] text-black relative overflow-hidden group">
                <i class="fa-solid fa-star absolute -right-2 -bottom-2 text-6xl opacity-10 group-hover:scale-125 transition"></i>
                <p class="text-[10px] uppercase font-black opacity-60 italic">Réservations</p>
                <h3 class="text-4xl font-bold my-2"><?= $total_reservations ?></h3>
                <p class="text-[9px] font-bold uppercase">Mois en cours</p>
            </div>
            <div class="bg-zinc-900/50 border border-zinc-800 p-6 rounded-[2.5rem]">
                <p class="text-[10px] uppercase font-bold text-zinc-500 italic">Check-ins</p>
                <h3 class="text-4xl font-light text-white my-2"><?= $clients_du_jour ?></h3>
                <p class="text-[9px] text-green-500 font-bold uppercase italic">Aujourd'hui</p>
            </div>
            <div class="bg-zinc-900/50 border border-zinc-800 p-6 rounded-[2.5rem]">
                <p class="text-[10px] uppercase font-bold text-zinc-500 italic">Alertes Stock</p>
                <h3 class="text-4xl font-light <?= ($alertes_stock > 0) ? 'text-red-500' : 'text-white' ?> my-2"><?= $alertes_stock ?></h3>
                <p class="text-[9px] text-zinc-600 font-bold uppercase italic">Urgences</p>
            </div>
            <div class="bg-zinc-900/50 border border-zinc-800 p-6 rounded-[2.5rem] flex flex-col items-center justify-center text-center hover:bg-[#D4AF37] hover:text-black transition duration-500">
                <i class="fa-solid fa-plus text-[#D4AF37] mb-2"></i>
                <p class="text-[9px] uppercase font-bold tracking-widest">Nouveau Dossier</p>
            </div>
        </div>

        <section class="video-container shadow-2xl">
            <video autoplay muted loop playsinline poster="https://images.pexels.com/photos/189296/pexels-photo-189296.jpeg">
                <source src="https://player.vimeo.com/external/434045526.sd.mp4?s=c27dbed09f2913e112e5f3068e273f59e66c7f53&profile_id=164&oauth2_token_id=57447761" type="video/mp4">
            </video>
            <div class="video-overlay flex flex-col items-center justify-center text-center">
                <h1 class="text-white text-6xl font-serif italic mb-4 animate-pulse">L'Excellence du Bémar</h1>
                <div class="h-[1px] w-24 bg-[#D4AF37] mb-4"></div>
                <p class="text-[#D4AF37] text-xs tracking-[0.6em] uppercase font-black">Prestige • Confort • Discrétion</p>
                <p class="text-[#D4AF37] text-xs tracking-[0.6em] uppercase font-bold"><i>Vivez le luxe au cœur d'Abidjan</i></p>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 bg-zinc-900/20 rounded-[2.5rem] border border-zinc-800 p-8">
                <h4 class="text-white text-xs font-bold uppercase tracking-[0.2em] mb-8 flex items-center gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4AF37]"></span> Historique Récent
                </h4>
                <div class="space-y-6">
                    <?php foreach($activites as $act): ?>
                    <div class="flex items-center gap-6 group hover:bg-white/5 p-3 rounded-2xl transition">
                        <span class="text-[10px] text-zinc-600 font-mono"><?= date('H:i', strtotime($act['date_action'])) ?></span>
                        <p class="text-sm text-zinc-300 flex-1 italic"><span class="text-white font-bold not-italic"><?= $act['nom'] ?></span> <?= $act['action'] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-[#D4AF37] rounded-[2.5rem] p-8 text-black shadow-2xl shadow-[#D4AF37]/10">
                <h4 class="text-[10px] font-black uppercase tracking-widest mb-10 text-center border-b border-black/10 pb-4">Statut des Chambres</h4>
                <div class="space-y-10">
                    <div>
                        <div class="flex justify-between items-end mb-3 text-xs font-black uppercase">
                            <span>Suites </span>
                            <span><?= $suites_data['dispo'] ?> / <?= $suites_data['total'] ?></span>
                        </div>
                        <div class="h-1.5 bg-black/10 rounded-full overflow-hidden">
                            <div class="h-full bg-black transition-all duration-1000" style="width: <?= ($suites_data['total'] > 0) ? ($suites_data['dispo']/$suites_data['total']*100) : 0 ?>%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-3 text-xs font-black uppercase">
                            <span>Standards</span>
                            <span><?= $deluxe_data['dispo'] ?> / <?= $deluxe_data['total'] ?></span>
                        </div>
                        <div class="h-1.5 bg-black/10 rounded-full overflow-hidden">
                            <div class="h-full bg-white transition-all duration-1000" style="width: <?= ($deluxe_data['total'] > 0) ? ($deluxe_data['dispo']/$deluxe_data['total']*100) : 0 ?>%"></div>
                        </div>
                    </div>
                </div>
                <a href="../reception/chambres.php" class="block w-full bg-black text-white text-[9px] font-black uppercase py-5 rounded-2xl mt-12 text-center hover:scale-105 transition shadow-xl">
                    Service d'étage
                </a>
            </div>
        </div>
    </div>
</main>

<script>
    // Horloge temps réel Abidjan
    function updateClock() {
        const now = new Date();
        const options = { timeZone: 'Africa/Abidjan', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        document.getElementById('clock').textContent = new Intl.DateTimeFormat('fr-FR', options).format(now);
    }
    setInterval(updateClock, 1000);

    // Loader
    window.addEventListener('load', () => {
        setTimeout(() => {
            const loader = document.getElementById('loader');
            loader.style.opacity = '0';
            setTimeout(() => loader.style.display = 'none', 700);
        }, 1000);
    });
</script>

<?php include '../layout/footer.php'; ?>


</body>
</html>