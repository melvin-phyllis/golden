<?php
session_start();
require_once '../../config/db.php';

$message = "";

// --- TRAITEMENT DU FORMULAIRE ---
if (isset($_POST['valider_mouvement'])) {
    $id_art = $_POST['article_id'];
    $quantite = intval($_POST['quantite']);
    $type = $_POST['type_mouvement']; // 'Entree' ou 'Sortie'
    $motif = $_POST['motif'];

    try {
        $pdo->beginTransaction();

        // 1. Mise à jour de la table menu_items
        if ($type == 'Entree') {
            $sql = "UPDATE menu_items SET stock_actuel = stock_actuel + ? WHERE id = ?";
        } else {
            $sql = "UPDATE menu_items SET stock_actuel = stock_actuel - ? WHERE id = ?";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$quantite, $id_art]);

        // 2. Enregistrement dans l'historique (Table stocks)
        // Note : Assure-toi que ta table 'stocks' a les colonnes : article_id, type_mouvement, quantite, motif, date_mouvement
        $sqlHist = "INSERT INTO stocks (article_id, type_mouvement, quantite, motif, date_mouvement) 
                    VALUES (?, ?, ?, ?, NOW())";
        $stmtHist = $pdo->prepare($sqlHist);
        $stmtHist->execute([$id_art, $type, $quantite, $motif]);

        $pdo->commit();
        $message = "success";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "error";
    }
}

// Récupération de la liste des produits pour le menu déroulant
$articles = $pdo->query("SELECT id, nom_article FROM menu_items ORDER BY nom_article ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mouvements de Stock | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
    </style>
</head>
<body class="p-4 md:p-10 flex flex-col items-center">

    <div class="w-full max-w-lg">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-xl font-serif text-[#D4AF37] uppercase tracking-widest"> Mouvement de Stock</h1>
            <a href="stock_dashboard.php" class="text-[10px] text-zinc-500 hover:text-white uppercase">Annuler</a>
        </div>

        <?php if($message == "success"): ?>
            <div class="bg-green-500/10 border border-green-500/50 text-green-500 p-4 rounded-2xl mb-6 text-xs text-center">
                Mouvement enregistré avec succès !
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-zinc-900 p-8 rounded-3xl gold-border space-y-6 shadow-2xl">
            
            <div>
                <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Produit concerné</label>
                <select name="article_id" required class="w-full bg-black border border-zinc-800 p-4 rounded-xl text-sm gold-text outline-none focus:border-[#D4AF37]">
                    <option value="">-- Choisir un article --</option>
                    <?php foreach($articles as $a): ?>
                        <option value="<?= $a['id'] ?>"><?= $a['nom_article'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Action</label>
                    <select name="type_mouvement" class="w-full bg-black border border-zinc-800 p-4 rounded-xl text-sm outline-none">
                        <option value="Entree" class="text-green-500 font-bold">ENTRÉE (+)</option>
                        <option value="Sortie" class="text-red-500 font-bold">SORTIE (-)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Quantité</label>
                    <input type="number" name="quantite" value="1" min="1" required class="w-full bg-black border border-zinc-800 p-4 rounded-xl text-sm outline-none focus:border-[#D4AF37]">
                </div>
            </div>

            <div>
                <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Motif / Commentaire</label>
                <input type="text" name="motif" placeholder="Ex: Livraison fournisseur, Casse, Inventaire..." required class="w-full bg-black border border-zinc-800 p-4 rounded-xl text-sm outline-none focus:border-[#D4AF37]">
            </div>

            <button type="submit" name="valider_mouvement" class="w-full bg-[#D4AF37] text-black font-bold py-5 rounded-2xl uppercase text-xs tracking-widest hover:bg-yellow-600 transition shadow-lg">
                Valider le mouvement
            </button>
        </form>

        <p class="text-center text-zinc-600 text-[10px] mt-8 italic">
            Note : Chaque mouvement est enregistré dans l'historique pour l'audit.
        </p>
    </div>

</body>
</html>