<?php
session_start();
require_once '../../config/db.php';

// // Sécurité : Redirection si non connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../login.php');
//     exit();
// }



// 1. Calcul du chiffre d'affaires du jour (Chambres + Salles)
$aujourdhui = date('Y-m-d');

// Revenus Chambres
$stmtCh = $pdo->prepare("SELECT SUM(montant_total) as total FROM reservations WHERE DATE(date_arrivee) = ?");
$stmtCh->execute([$aujourdhui]);
$rev_chambres = $stmtCh->fetch()['total'] ?? 0;

// Revenus Salles
$stmtSa = $pdo->prepare("SELECT SUM(montant_total) as total FROM reservations_salles WHERE date_reservation = ?");
$stmtSa->execute([$aujourdhui]);
$rev_salles = $stmtSa->fetch()['total'] ?? 0;

$total_jour = $rev_chambres + $rev_salles;


// Compte les chambres qui ont besoin d'attention (statut 'À nettoyer')
$stmtDirty = $pdo->query("SELECT COUNT(*) FROM chambres WHERE statut = 'À nettoyer'");
$chambres_sales = $stmtDirty->fetchColumn();

// 2. Taux d'occupation des chambres
$stmtOcc = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN statut = 'Occupée' THEN 1 ELSE 0 END) as occupees FROM chambres");
$occ_data = $stmtOcc->fetch();
$taux = ($occ_data['total'] > 0) ? round(($occ_data['occupees'] / $occ_data['total']) * 100) : 0;



// 3. Récupération des notifications simplifiée pour éviter les erreurs de colonnes
try {
    $queryNotify = "SELECT 'Chambre' as type, c.nom_complet as client, r.date_arrivee as date_act, 'Nouvelle réservation' as action 
                    FROM reservations r 
                    JOIN clients c ON r.client_id = c.id 
                    UNION 
                    SELECT 'Salle' as type, 'Réservation Espace' as client, date_reservation as date_act, 'Location d\'espace' as action 
                    FROM reservations_salles 
                    ORDER BY date_act DESC LIMIT 3";

    $notifications = $pdo->query($queryNotify)->fetchAll();
} catch (Exception $e) {
    // Si une table manque encore, on affiche un tableau vide pour ne pas bloquer le Dashboard
    $notifications = [];
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bemar Prestige | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
<body>

    <nav class="border-b border-zinc-800 bg-black/50 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 gold-gradient rounded-lg flex items-center justify-center font-bold text-black text-xl shadow-[0_0_15px_rgba(212,175,55,0.3)]">B</div>
                <span class="font-serif tracking-widest text-lg hidden md:block">BEMAR <span class="text-[#D4AF37]">PRESTIGE</span></span>
            </div>

            <div class="flex items-center gap-8">
                <a href="../admin/accueil.php" class="nav-link text-[#D4AF37]">Accueil</a>
                <a href="../manager/manager_dashboard.php" class="nav-link">Mon dash</a>
                
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




<div class="max-w-7xl mx-auto px-8 mt-6">
    <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar">
        <?php foreach($notifications as $note): ?>
            <div class="flex-none bg-zinc-900/80 border-l-2 border-[#D4AF37] p-4 rounded-r-xl min-w-[300px] flex items-center gap-4 animate-fade-in-down">
                <div class="bg-[#D4AF37]/10 p-2 rounded-full">
                    <?php echo $note['type'] == 'Chambre' ? '🏨' : '🏛️'; ?>
                </div>
                <div>
                    <p class="text-[10px] text-[#D4AF37] font-bold uppercase tracking-widest"><?= $note['action'] ?></p>
                    <p class="text-xs font-medium"><?= htmlspecialchars($note['client']) ?></p>
                    <p class="text-[9px] text-zinc-500 italic"><?= date('H:i', strtotime($note['date_act'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if(empty($notifications)): ?>
            <p class="text-zinc-600 text-[10px] uppercase italic">Aucune activité récente à signaler.</p>
        <?php endif; ?>
    </div>
</div>




    <div class="max-w-7xl mx-auto p-8">
        
        <header class="flex justify-between items-center mb-12 mt-4">
            <div>
                <h1 class="text-4xl font-serif tracking-[10px] uppercase text-[#D4AF37]">Bienvenue Manager</h1>
                <p class="text-zinc-500 uppercase text-[10px] tracking-widest mt-2">Vue d'ensemble de l'établissement Bemar</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-light text-zinc-400"><?= date('l d F Y') ?></p>
                <span class="text-[10px] bg-green-900/30 text-green-500 px-3 py-1 rounded-full border border-green-500/30">Serveur Local Actif</span>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-zinc-900/50 p-8 rounded-3xl gold-border">
                <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mb-2">Revenus du Jour</p>
                <h2 class="text-4xl font-mono"><?= number_format($total_jour, 0, ',', ' ') ?> <span class="text-xs text-[#D4AF37]">FCFA</span></h2>
            </div>




<div class="bg-zinc-900/50 p-8 rounded-3xl gold-border relative">
    <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mb-2">Service Gouvernance</p>
    <h2 class="text-4xl font-mono <?php echo $chambres_sales > 0 ? 'text-yellow-500' : 'text-white'; ?>">
        <?= $chambres_sales ?>
    </h2>
    <p class="text-[10px] mt-2 uppercase <?php echo $chambres_sales > 0 ? 'text-yellow-500 animate-pulse' : 'text-zinc-600'; ?>">
        <?php echo $chambres_sales > 0 ? '⚠️ Chambres à préparer' : 'Toutes les chambres sont prêtes'; ?>
    </p>
</div>


            <div class="bg-zinc-900/50 p-8 rounded-3xl gold-border">
                <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mb-2">Salles en Activité</p>
                <h2 class="text-4xl font-mono"><?= $pdo->query("SELECT COUNT(*) FROM reservations_salles WHERE date_reservation = '$aujourdhui'")->fetchColumn(); ?></h2>
                <p class="text-xs text-zinc-600 mt-4">Événements prévus aujourd'hui</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-16">
            <a href="../reception/chambres.php" class="p-6 bg-zinc-900 rounded-2xl border border-zinc-800 hover:border-[#D4AF37] transition group text-center">
                <span class="block text-2xl mb-2">🏨</span>
                <span class="text-xs font-medium uppercase tracking-widest">Chambres</span>
            </a>
            <a href="gestion_salles.php" class="p-6 bg-zinc-900 rounded-2xl border border-zinc-800 hover:border-[#D4AF37] transition group text-center">
                <span class="block text-2xl mb-2">🏛️</span>
                <span class="text-xs font-medium uppercase tracking-widest">Salles</span>
            </a>
            <a href="../reception/reservation.php" class="p-6 bg-zinc-900 rounded-2xl border border-zinc-800 hover:border-[#D4AF37] transition group text-center">
                <span class="block text-2xl mb-2">📋</span>
                <span class="text-xs font-medium uppercase tracking-widest">Registre</span>
            </a>
            <a href="print_report.php" class="p-6 bg-zinc-900 rounded-2xl border border-zinc-800 hover:border-[#D4AF37] transition group text-center">
                <span class="block text-2xl mb-2">📊</span>
                <span class="text-xs font-medium uppercase tracking-widest">Rapports</span>
            </a>
        </div>

        <div class="relative w-full h-[400px] rounded-3xl overflow-hidden gold-border shadow-2xl">
            <div class="absolute inset-0 bg-black/40 z-10 flex flex-col items-center justify-center text-center p-6">
                <h3 class="text-3xl font-serif uppercase tracking-[5px] mb-2">L'Expérience Bemar</h3>
                <p class="text-[#D4AF37] text-xs tracking-widest uppercase">Luxe • Confort • Prestige</p>
            </div>
            <video class="w-full h-full object-cover" autoplay loop muted playsinline>

                <!-- c'est parce que j'ai pas hotel_promo.mp4 c'est pour j'ai sauter pour un deuxieme  -->
                <source src="../../assets/videos/hotel_promo.mp4" type="video/mp4">

                <source src="https://www.w3schools.com/html/mov_bbb.mp4" type="video/mp4">

                Votre navigateur ne supporte pas la vidéo.
            </video>
        </div>

    </div>

    <!-- <footer class="mt-20 py-10 border-t border-zinc-900 text-center">
        <p class="text-zinc-600 text-[10px] uppercase tracking-widest">© <?= date('Y') ?> Bemar Prestige Hotel Management System</p>
    </footer> -->
<?php include '../layout/footer3.php'; ?>
</body>
</html>