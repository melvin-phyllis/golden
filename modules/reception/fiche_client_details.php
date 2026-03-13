<?php
session_start();
require_once '../../config/db.php';

if (!isset($_GET['id'])) { header('Location: clients.php'); exit(); }
$client_id = intval($_GET['id']);

// 1. Récupérer les infos fixes du client
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$client_id]);
$client = $stmt->fetch();

// 2. Récupérer le dernier séjour pour avoir la pièce d'identité et les préférences
$stmt_res = $pdo->prepare("SELECT * FROM reservations WHERE client_id = ? ORDER BY date_arrivee DESC LIMIT 1");
$stmt_res->execute([$client_id]);
$last_res = $stmt_res->fetch();

if (!$client) { die("Client introuvable."); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Client | <?= $client['nom_complet'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-[#050505] text-white p-8">

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <a href="clients.php" class="text-zinc-500 hover:text-[#D4AF37] transition"><i class="fa-solid fa-arrow-left mr-2"></i> Retour à la base</a>
            <div class="text-right">
                <span class="bg-[#D4AF37] text-black text-[10px] font-black px-4 py-1 rounded-full uppercase tracking-tighter">Profil Vérifié</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-zinc-900/50 border border-zinc-800 p-8 rounded-[2.5rem] text-center">
                    <div class="w-24 h-24 rounded-full bg-zinc-800 border-2 border-[#D4AF37] mx-auto mb-4 flex items-center justify-center text-3xl font-serif text-[#D4AF37]">
                        <?= substr($client['nom_complet'], 0, 1) ?>
                    </div>
                    <h2 class="text-xl font-bold italic"><?= $client['nom_complet'] ?></h2>
                    <p class="text-zinc-500 text-sm"><?= $client['telephone'] ?></p>
                </div>

                <div class="bg-zinc-900/30 border border-dashed border-zinc-800 p-6 rounded-[2rem]">
                    <p class="text-[10px] text-zinc-500 uppercase font-bold mb-4 tracking-widest">Document Officiel</p>
                    <div class="flex items-center gap-4">
                        <i class="fa-solid fa-id-card text-2xl text-[#D4AF37]"></i>
                        <div>
                            <p class="text-xs text-zinc-400"><?= $last_res['type_piece'] ?? 'Non renseigné' ?></p>
                            <p class="font-mono text-sm tracking-tighter"><?= $last_res['num_piece'] ?? '------------' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-zinc-900/50 border border-zinc-800 p-8 rounded-[2.5rem]">
                    <h3 class="text-[#D4AF37] text-xs font-black uppercase mb-6 tracking-widest border-l-2 border-[#D4AF37] pl-4">Préférences Signature</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-black/40 p-4 rounded-2xl border border-zinc-800">
                            <p class="text-[9px] text-zinc-500 uppercase mb-1">Accueil Service</p>
                            <p class="text-sm italic"><?= $last_res['accueil_service'] ?? 'Standard' ?></p>
                        </div>
                        <div class="bg-black/40 p-4 rounded-2xl border border-zinc-800">
                            <p class="text-[9px] text-zinc-500 uppercase mb-1">Confort Sommeil</p>
                            <p class="text-sm italic"><?= $last_res['confort_sommeil'] ?? 'Standard' ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-zinc-900/20 border border-zinc-800 rounded-[2.5rem] overflow-hidden">
                    <div class="p-6 border-b border-zinc-800 bg-zinc-900/40">
                        <h3 class="text-white text-xs font-bold uppercase tracking-widest">Dernières Activités</h3>
                    </div>
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-zinc-500 border-b border-zinc-800/50">
                                <th class="p-4">Date</th>
                                <th class="p-4">Chambre/Salle</th>
                                <th class="p-4 text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $history = $pdo->prepare("SELECT * FROM reservations WHERE client_id = ? ORDER BY date_arrivee DESC");
                            $history->execute([$client_id]);
                            foreach($history->fetchAll() as $h):
                            ?>
                            <tr class="border-b border-zinc-800/30 hover:bg-white/[0.02]">
                                <td class="p-4 text-zinc-400 font-mono"><?= date('d/m/Y', strtotime($h['date_arrivee'])) ?></td>
                                <td class="p-4 italic">Suite #<?= $h['chambre_id'] ?></td>
                                <td class="p-4 text-right font-bold"><?= number_format($h['montant_total'], 0, ',', ' ') ?> CFA</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <?php include '../layout/footer.php'; ?>

</body>
</html>