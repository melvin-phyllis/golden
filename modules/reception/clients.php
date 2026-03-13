<?php
session_start();
require_once '../../config/db.php';

// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Réceptionniste') {
//     header('Location: ../../login.php');
//     exit();
// }

// Récupération de la liste des clients et leur dernier séjour
$query = "SELECT c.*, 
          (SELECT MAX(date_arrivee) FROM reservations WHERE client_id = c.id) as dernier_sejour,
          (SELECT COUNT(*) FROM reservations WHERE client_id = c.id) as nb_sejours
          FROM clients c 
          ORDER BY nb_sejours DESC";
$clients = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiches Clients | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-text { color: #D4AF37; }
        .client-card { background: #111; transition: 0.3s; }
        .client-card:hover { border-color: #D4AF37; transform: translateY(-3px); }
    </style>
</head>
<body class="p-8">

    <div class="flex justify-between items-end mb-12 border-b border-zinc-800 pb-6">
        <div>
            <h1 class="text-3xl font-serif gold-text uppercase tracking-widest">Base de Données Clients</h1>
            <p class="text-gray-500 italic">Connaître vos hôtes pour mieux les servir</p>
        </div>
        <div class="relative">
            <input type="text" placeholder="Rechercher un nom..." class="bg-zinc-900 border border-zinc-800 rounded-full px-6 py-2 text-sm focus:outline-none focus:border-[#D4AF37]">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($clients as $cl): ?>
            <div class="client-card gold-border p-6 rounded-2xl relative overflow-hidden">
                <?php if($cl['nb_sejours'] > 3): ?>
                    <div class="absolute top-0 right-0 bg-[#D4AF37] text-black text-[9px] font-bold px-3 py-1 uppercase">VIP Gold Bémar</div>
                <?php endif; ?>

                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-zinc-800 flex items-center justify-center text-xl font-serif gold-text border border-zinc-700">
                        <?php echo substr($cl['nom_complet'], 0, 1); ?>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold"><?php echo $cl['nom_complet']; ?></h3>
                        <p class="text-xs text-gray-500"><?php echo $cl['telephone']; ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-zinc-800 pt-4 mt-4">
                <div>
                   <p class="text-[10px] text-gray-500 uppercase">Séjours</p>
                  <p class="font-bold"><?php echo $cl['nb_sejours']; ?></p>
                </div>
                <div>
                   <p class="text-[10px] text-gray-500 uppercase">Dernière visite</p>
                  <p class="text-sm"><?php echo $cl['dernier_sejour'] ? date('d/m/Y', strtotime($cl['dernier_sejour'])) : 'N/A'; ?></p>
                </div>
                <div class="col-span-2 mt-2 pt-2 border-t border-zinc-900">
                    <p class="text-[10px] text-gray-500 uppercase">Document d'identité</p>
                    <p class="text-xs gold-text font-mono tracking-wider">
                      <?php echo !empty($cl['type_piece']) ? $cl['type_piece'] . " : " . $cl['num_piece'] : "Aucune pièce enregistrée"; ?>
                    </p>
                </div>
</div>

                <div class="mt-6 flex space-x-2">
                    <a href="client_details.php?id=<?php echo $cl['id']; ?>" class="flex-1 bg-zinc-800 text-center py-2 rounded-lg text-[10px] font-bold uppercase hover:bg-zinc-700 transition">Historique</a>
                    <a href="https://wa.me/<?php echo $cl['telephone']; ?>" class="w-10 bg-green-900/20 text-green-500 flex items-center justify-center rounded-lg hover:bg-green-900/40 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.55 4.103 1.524 5.834L0 24l6.326-1.654C7.886 23.402 9.873 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z"/></svg>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>