<?php
session_start();
require_once '../../config/db.php';

// --- LOGIQUE DE SUPPRESSION ---
if (isset($_GET['delete_id'])) {
    $id_a_supprimer = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->execute([$id_a_supprimer]);
        header("Location: menu_items.php?msg=deleted");
        exit();
    } catch (PDOException $e) {
        $error = "Impossible de supprimer cet article (il est peut-être lié à une commande).";
    }
}

// --- AJOUT D'UN ARTICLE ---
if (isset($_POST['ajouter_item'])) {
    $nom = $_POST['nom'];
    $cat = $_POST['categorie'];
    $prix = $_POST['prix'];
    $img = $_POST['image_url'];
    $stock = !empty($_POST['stock_initial']) ? $_POST['stock_initial'] : 0;

    $stmt = $pdo->prepare("INSERT INTO menu_items (nom_article, categorie, prix_unitaire, image_url, stock_actuel) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $cat, $prix, $img, $stock]);
    
    header("Location: menu_items.php?success=1");
    exit();
}

// Récupération des articles
$items = $pdo->query("SELECT * FROM menu_items ORDER BY categorie ASC, nom_article ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Menu | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .delete-btn { opacity: 0; transition: 0.3s; }
        .item-card:hover .delete-btn { opacity: 1; }
    </style>
</head>
<body class="p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-serif text-[#D4AF37] mb-8 uppercase tracking-widest text-center">Gestion de la Carte</h1>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-3 rounded-xl mb-6 text-center text-xs">Article supprimé de la carte.</div>
        <?php endif; ?>

        <div class="bg-zinc-900 p-6 rounded-3xl gold-border mb-10 shadow-2xl"> 
            <form method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="text" name="nom" placeholder="Nom de l'article" class="bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]" required>
                <select name="categorie" class="bg-black border border-zinc-800 p-3 rounded-xl outline-none">
                    <option>BOISSONS</option>
                    <option>PLATS</option>
                    <option>COCKTAILS</option>
                    <option>AUTRE</option>
                </select>
                <input type="number" name="prix" placeholder="Prix (FCFA)" class="bg-black border border-zinc-800 p-3 rounded-xl outline-none" required>
                <input type="number" name="stock_initial" placeholder="Stock" class="bg-black border border-zinc-800 p-3 rounded-xl outline-none">
                <input type="text" name="image_url" placeholder="Lien image" class="bg-black border border-zinc-800 p-3 rounded-xl outline-none">
                <button type="submit" name="ajouter_item" class="bg-[#D4AF37] text-black font-bold rounded-xl uppercase text-[10px]">Ajouter</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach($items as $i): ?>
            <div class="item-card bg-zinc-900 p-4 rounded-2xl border border-zinc-800 flex items-center space-x-4 relative">
                
                <a href="menu_items.php?delete_id=<?= $i['id'] ?>" 
                   onclick="return confirm('Voulez-vous vraiment retirer cet article de la carte ?')"
                   class="delete-btn absolute -top-2 -right-2 bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] shadow-lg hover:bg-red-700">
                   ✕
                </a>

                <img src="<?= !empty($i['image_url']) ? $i['image_url'] : 'https://via.placeholder.com/150'; ?>" class="w-14 h-14 object-cover rounded-xl bg-zinc-800">
                
                <div class="overflow-hidden">
                    <h3 class="font-bold text-xs truncate uppercase"><?= $i['nom_article']; ?></h3>
                    <p class="text-[#D4AF37] text-[10px] font-mono"><?= number_format($i['prix_unitaire'], 0, ',', ' '); ?> F</p>
                    <p class="text-[9px] text-zinc-500">Stock : <?= $i['stock_actuel']; ?></p>
                </div>
            </div>
            <?php endforeach; ?> 

            
        </div>

        <div class="mt-16 text-center">
            <a href="pos_bar.php" class="text-[#D4AF37] border-b border-[#D4AF37] pb-1 uppercase text-[10px] tracking-widest hover:text-white transition">Accéder au Terminal de vente(POS)</a>
        </div>
    </div>


    <?php include '../layout/footer.php'; ?>
</body>
</html>