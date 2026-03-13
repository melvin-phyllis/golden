<?php
session_start();
require_once '../../config/db.php';

// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Réceptionniste') {
//     header('Location: ../../login.php');
//     exit();
// }

// 1. Récupération du terme de recherche
$search = $_GET['search'] ?? '';

// 2. Requête filtrée (recherche par nom, numéro de chambre ou pièce d'identité)
$query = "SELECT r.*, c.nom_complet, ch.numero_chambre, t.tarif_nuit, t.libelle 
          FROM reservations r
          JOIN clients c ON r.client_id = c.id
          JOIN chambres ch ON r.chambre_id = ch.id
          JOIN types_chambre t ON ch.type_id = t.id";

if (!empty($search)) {
    $query .= " WHERE c.nom_complet LIKE :search 
                OR ch.numero_chambre LIKE :search 
                OR r.num_piece LIKE :search";
}

$query .= " ORDER BY r.date_arrivee DESC";

$stmt = $pdo->prepare($query);
if (!empty($search)) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->execute();
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Registre | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        .search-input { background: #111; border: 1px solid #333; color: white; padding: 10px 20px; border-radius: 99px; outline: none; width: 300px; transition: 0.3s; }
        .search-input:focus { border-color: #D4AF37; box-shadow: 0 0 10px rgba(212, 175, 55, 0.2); }
    </style>
</head>

<body class="p-8">

<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
    <div>
        <h1 class="text-3xl font-serif gold-text uppercase tracking-widest">Registre des Séjours</h1>
        <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-tighter">Historique et suivi des résidents</p>
    </div>

    <form method="GET" class="flex items-center gap-2">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
               placeholder="Nom, N° Chambre ou Pièce..." class="search-input text-xs">
        <button type="submit" class="bg-[#D4AF37] text-black px-4 py-2 rounded-full text-[10px] font-bold uppercase hover:scale-105 transition">
            Chercher
        </button>
        <?php if(!empty($search)): ?>
            <a href="registre.php" class="text-zinc-500 text-[10px] uppercase hover:text-white">Annuler</a>
        <?php endif; ?>
    </form>
    <a href="print_registre.php?search=<?php echo urlencode($search); ?>" 
       target="_blank"
        class="bg-zinc-800 text-white px-4 py-2 rounded-full text-[10px] font-bold uppercase border border-zinc-700 hover:border-[#D4AF37] transition">
        📄 Exporter PDF
    </a>
</div>

<div class="overflow-x-auto gold-border rounded-2xl bg-zinc-900/50">
    <table class="w-full text-left">
        <thead class="text-[10px] uppercase text-gray-500 border-b border-zinc-800">
            <tr>
                <th class="p-6">Client & Identité</th>
                <th class="p-6">Chambre</th>
                <th class="p-6">Période</th>
                <th class="p-6 text-right">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php if(empty($reservations)): ?>
                <tr>
                    <td colspan="4" class="p-20 text-center text-zinc-500 italic">Aucun résultat trouvé pour "<?php echo htmlspecialchars($search); ?>"</td>
                </tr>
            <?php else: ?>
                <?php foreach($reservations as $res): 
                    $start = new DateTime($res['date_arrivee']);
                    $end = new DateTime($res['date_depart']);
                    $nuits = $start->diff($end)->days ?: 1;
                ?>
                <tr class="hover:bg-zinc-800/30 border-b border-zinc-900 transition">
                    <td class="p-6">
                        <p class="font-bold text-lg"><?php echo htmlspecialchars($res['nom_complet']); ?></p>
                        <p class="text-[10px] text-[#D4AF37] uppercase">
                            <?php echo htmlspecialchars($res['type_piece']); ?> : <span class="text-gray-400"><?php echo htmlspecialchars($res['num_piece']); ?></span>
                        </p>
                    </td>

                    <td class="p-6">
                        <span class="gold-text font-bold">N° <?php echo htmlspecialchars($res['numero_chambre']); ?></span>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($res['libelle']); ?></p>
                    </td>

                    <td class="p-6">
                        <div class="text-sm font-medium">
                            <?php echo $start->format('d/m'); ?> au <?php echo $end->format('d/m/Y'); ?>
                        </div>
                        <p class="text-[10px] text-gray-500 italic"><?php echo $nuits; ?> nuit(s) • <?php echo number_format($nuits * $res['tarif_nuit'], 0, ',', ' '); ?> FCFA</p>
                    </td>

                    <td class="p-6 text-right">

                     <a href="print_welcome.php?id=<?php echo $res['id']; ?>" target="_blank" class="border border-zinc-700 text-zinc-400 px-4 py-1.5 rounded text-[10px] font-bold uppercase hover:border-[#D4AF37] hover:text-[#D4AF37]">Carte</a>


                            <div class="flex justify-end gap-2">
                        <?php if ($res['statut'] !== 'Terminée'): ?>
                        <a href="check_out.php?id=<?php echo $res['id']; ?>" 
                            onclick="return confirm('Confirmer le départ du client et libérer la chambre ?');"
                            class="bg-[#D4AF37] text-black px-4 py-1.5 rounded text-[10px] font-bold uppercase hover:bg-yellow-600 transition">
                            Libérer(Check-out)
                        </a>
                        <?php else: ?>
                             <span class="text-[10px] text-zinc-500 uppercase border border-zinc-800 px-3 py-1.5 rounded">
                             Terminée
                        </span>
                        <?php endif; ?>

                        <a href="facture.php?res_id=<?php echo $res['id']; ?>" 
                         class="bg-white text-black px-4 py-1.5 rounded text-[10px] font-bold uppercase hover:bg-gray-200">
                           Facture
                        </a>
                        
                           
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>



<?php include '../layout/footer.php'; ?>
</body>
</html>