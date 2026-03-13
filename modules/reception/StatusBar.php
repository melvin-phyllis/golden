<?php
// On vérifie si $pdo existe, sinon on tente de le charger
if (!isset($pdo)) {
    require_once '../../config/db.php';
}

try {
    // Calcul des statistiques en temps réel
    $statsQuery = $pdo->query("
        SELECT 
            SUM(CASE WHEN statut = 'Libre' THEN 1 ELSE 0 END) as libres,
            SUM(CASE WHEN statut = 'À nettoyer' THEN 1 ELSE 0 END) as sales,
            SUM(CASE WHEN statut = 'Occupée' THEN 1 ELSE 0 END) as occupees
        FROM chambres
    ")->fetch();

    // Estimation du chiffre d'affaires du jour
    $recetteQuery = $pdo->query("
        SELECT SUM(t.tarif_nuit) as total_jour 
        FROM reservations r 
        JOIN chambres c ON r.chambre_id = c.id 
        JOIN types_chambre t ON c.type_id = t.id 
        WHERE r.statut = 'Occupée'
    ")->fetch();
} catch (PDOException $e) {
    // En cas d'erreur de base de données, on initialise à zéro pour éviter le plantage
    $statsQuery = ['libres' => 0, 'sales' => 0, 'occupees' => 0];
    $recetteQuery = ['total_jour' => 0];
}
?>


<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">
    <div class="bg-zinc-900/40 border border-green-900/30 p-4 rounded-2xl flex items-center justify-between">
        <div>
            <p class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Prêtes à la vente</p>
            <p class="text-2xl font-serif text-green-500"><?php echo $statsQuery['libres'] ?? 0; ?> <span class="text-xs italic">Unités</span></p>
        </div>
        <div class="h-10 w-10 rounded-full bg-green-500/10 flex items-center justify-center">
            <div class="h-2 w-2 bg-green-500 rounded-full animate-pulse"></div>
        </div>
    </div>

    <div class="bg-zinc-900/40 border border-yellow-900/30 p-4 rounded-2xl flex items-center justify-between">
        <div>
            <p class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Gouvernance requise</p>
            <p class="text-2xl font-serif text-yellow-500"><?php echo $statsQuery['sales'] ?? 0; ?> <span class="text-xs italic">Sales</span></p>
        </div>
        <div class="h-10 w-10 rounded-full bg-yellow-500/10 flex items-center justify-center text-yellow-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
    </div>

    <div class="bg-zinc-900/40 border border-red-900/30 p-4 rounded-2xl flex items-center justify-between">
        <div>
            <p class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Occupation</p>
            <p class="text-2xl font-serif text-red-500"><?php echo $statsQuery['occupees'] ?? 0; ?> <span class="text-xs italic">Hôtes</span></p>
        </div>
        <div class="h-10 w-10 rounded-full bg-red-500/10 flex items-center justify-center text-red-500 text-xs font-bold">
            <?php 
                $total = ($statsQuery['libres'] ?? 0) + ($statsQuery['sales'] ?? 0) + ($statsQuery['occupees'] ?? 0);
                echo $total > 0 ? round(($statsQuery['occupees'] / $total) * 100) : 0; 
            ?>%
        </div>
    </div>

    <div class="bg-zinc-900/40 border border-[#D4AF37]/30 p-4 rounded-2xl flex items-center justify-between">
        <div>
            <p class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">Recette en cours</p>
            <p class="text-2xl font-serif gold-text"><?php echo number_format($recetteQuery['total_jour'] ?? 0, 0, ',', ' '); ?> <span class="text-[10px]">FCFA</span></p>
        </div>
        <div class="h-10 w-10 rounded-full bg-[#D4AF37]/10 flex items-center justify-center gold-text text-xs italic">
            CA
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>