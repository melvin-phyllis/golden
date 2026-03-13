<?php
session_start();
require_once '../../config/db.php';

// 1. GESTION DE LA CONNEXION (QR ou FORMULAIRE)
// Si on reçoit un ID par l'URL (QR Code)
if (isset($_GET['emp'])) {
    $_SESSION['employe_id'] = $_GET['emp'];
} 
// Si on reçoit un ID par le formulaire manuel
elseif (isset($_POST['emp_manuel'])) {
    $_SESSION['employe_id'] = $_POST['emp_manuel'];
}

// 2. VÉRIFICATION DE L'IDENTITÉ
$info_emp = null;
if (isset($_SESSION['employe_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM equipe_prestige WHERE id = ?");
    $stmt->execute([$_SESSION['employe_id']]);
    $info_emp = $stmt->fetch();

    // Si l'ID dans la session est faux, on vide la session
    if (!$info_emp) {
        unset($_SESSION['employe_id']);
    }
}

// 3. RÉCUPÉRATION DES DONNÉES POUR LE FORMULAIRE
$chambres = $pdo->query("SELECT id, numero_chambre FROM chambres ORDER BY numero_chambre ASC")->fetchAll();

// Bouton Déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: portail_equipe.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portail Collaborateur | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-card { background: linear-gradient(145deg, #111, #080808); border: 1px solid rgba(212, 175, 55, 0.2); }
        .input-premium { background: #000; border: 1px solid #222; color: white; }
        .input-premium:focus { border-color: #D4AF37; }
    </style>
</head>
<body class="p-4">

    <?php if (!$info_emp): ?>
        <div class="max-w-md mx-auto mt-20 p-8 gold-card rounded-[2.5rem] text-center shadow-2xl">
            <div class="text-4xl mb-6">🔑</div>
            <h2 class="text-[#D4AF37] font-serif text-2xl mb-2 uppercase tracking-widest">Espace Équipe</h2>
            <p class="text-[10px] text-zinc-500 mb-8 uppercase tracking-[0.2em]">Entrez votre code personnel</p>
            
            <form method="POST" action="portail_equipe.php" class="space-y-4">
                <input type="number" name="emp_manuel" placeholder="----" required 
                       class="w-full input-premium p-6 rounded-2xl text-center font-mono text-3xl outline-none">
                
                <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-5 rounded-2xl uppercase text-xs tracking-widest hover:bg-yellow-600 transition">
                    Se connecter
                </button>
            </form>
        </div>

    <?php else: ?>
        <div class="max-w-lg mx-auto">
            <header class="flex justify-between items-center mb-8 bg-zinc-900/50 p-4 rounded-2xl border border-zinc-800">
                <div>
                    <p class="text-[9px] text-zinc-500 uppercase">Collaborateur</p>
                    <h1 class="text-sm font-bold uppercase text-[#D4AF37]"><?= $info_emp['nom_complet'] ?></h1>
                </div>
                <a href="?logout=1" class="text-[9px] bg-red-900/20 text-red-400 border border-red-900/50 px-3 py-2 rounded-lg uppercase font-bold">Quitter</a>

                <a href="../reception/gouvernance.php" class="bg-zinc-800 text-xs px-4 py-2 rounded-lg border border-zinc-700 hover:bg-[#D4AF37]">Retour gouverce</a>
            </header>

            <div class="gold-card p-6 rounded-[2rem] mb-6">
                <h2 class="text-xs font-bold mb-5 uppercase tracking-widest flex items-center gap-2">
                    <span class="text-[#D4AF37]">🛠️</span> Signaler un problème
                </h2>
                
                <form action="traitement_signalement.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="employe_id" value="<?= $info_emp['id'] ?>">
                    
                    <div>
                        <label class="text-[9px] text-zinc-500 ml-2 uppercase">Localisation</label>
                        <select name="chambre_id" required class="w-full input-premium p-4 rounded-xl text-sm mt-1 outline-none">
                            <option value="">Choisir la chambre...</option>
                            <?php foreach($chambres as $ch): ?>
                                <option value="<?= $ch['id'] ?>">Chambre <?= $ch['numero_chambre'] ?></option>
                            <?php endforeach; ?>
                            <option value="0">Zones Communes</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[9px] text-zinc-500 ml-2 uppercase">Description</label>
                        <textarea name="description" placeholder="Quel est le problème ?" required 
                                  class="w-full input-premium p-4 rounded-xl text-sm h-32 mt-1 outline-none"></textarea>
                    </div>

                    <div>
                        <label class="text-[9px] text-zinc-500 ml-2 uppercase">Photo (Appareil photo)</label>
                        <input type="file" name="photo_panne" accept="image/*" capture="environment" 
                               class="w-full text-[10px] text-zinc-500 mt-2">
                    </div>

                    <button type="submit" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-xl uppercase text-xs tracking-widest mt-4">
                        Envoyer le rapport
                    </button>
                </form>
            </div>

            <div class="bg-zinc-900/30 p-6 rounded-[2rem] border border-zinc-800">
                <h2 class="text-xs font-bold mb-4 uppercase tracking-widest flex items-center gap-2">
                    <span class="text-green-500">✨</span> Ménage terminé
                </h2>
                <form action="maj_menage.php" method="POST" class="flex gap-2">
                    <input type="hidden" name="employe_id" value="<?= $info_emp['id'] ?>">
                    <select name="chambre_id" required class="flex-1 input-premium p-3 rounded-xl text-xs outline-none">
                        <option value="">Sélectionner...</option>
                        <?php foreach($chambres as $ch): ?>
                            <option value="<?= $ch['id'] ?>">Chambre <?= $ch['numero_chambre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="bg-green-600 text-white px-5 py-3 rounded-xl text-[10px] font-bold uppercase">Valider</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

</body>
</html>