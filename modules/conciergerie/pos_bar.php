<?php
session_start();
require_once '../../config/db.php';

// Initialisation sécurisée des données
$articles = [];
$categories = [];
$chambres = [];

try {
    // Récupération des articles avec gestion flexible des noms de colonnes
    $stmt = $pdo->query("SELECT * FROM menu_items ORDER BY categorie ASC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Extraction des catégories pour les filtres tactiles
    $stmtCat = $pdo->query("SELECT DISTINCT categorie FROM menu_items WHERE categorie IS NOT NULL");
    $categories = $stmtCat->fetchAll(PDO::FETCH_COLUMN);

    // Liste des chambres occupées pour le lien facture
    $stmtCham = $pdo->query("SELECT id, numero_chambre FROM chambres WHERE statut = 'Occupée'");
    $chambres = $stmtCham->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur BDD : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>POS Prestige | Bar & Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; overflow: hidden; }
        .gold-text { color: #D4AF37; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.2); }
        .menu-grid { height: calc(100vh - 200px); overflow-y: auto; scrollbar-width: none; }
        .cart-area { height: calc(100vh - 480px); overflow-y: auto; }
        .item-card { cursor: pointer; transition: 0.2s; border-radius: 20px; }
        .item-card:active { transform: scale(0.95); background: #D4AF37; }
        @media print { .no-print { display: none; } #ticket-print { display: block !important; color: #000; } }
    </style>
</head>
<body class="flex h-screen no-print">

    <div class="flex-1 p-6">


    

        <div class="flex justify-between items-center mb-6">
            
            <div>
                <h1 class="text-3xl font-serif gold-text uppercase tracking-widest">Service (bar-restaurant) </h1>
                <p class="text-gray-500 text-xs italic">Point de Vente - Bar & Gastronomie Bémar</p>

                 <div class="mt-6">
            <a href="conciergerie_dash.php" class="text-zinc-500 text-xs uppercase hover:text-white">← Retour 
                
            </a>
        </div>
            </div>
            

            



            <a href="liste_commandes.php" class="text-[10px] bg-zinc-800 px-3 py-1 rounded-full hover:text-[#D4AF37]">
            📊 Voir les ventes
            </a>

            <div class="flex gap-2 overflow-x-auto pb-2">
                <button onclick="filterCat('all')" class="px-4 py-2 bg-zinc-900 border border-zinc-800 rounded-full text-[10px] uppercase">Tout</button>
                <?php foreach($categories as $cat): ?>
                    <button onclick="filterCat('<?= $cat ?>')" class="px-4 py-2 bg-zinc-900 border border-zinc-800 rounded-full text-[10px] uppercase"><?= $cat ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 menu-grid">
            <?php foreach($articles as $it): 
                // Détection dynamique des noms de colonnes pour éviter les erreurs "Undefined key"
                $nom = $it['nom_article'] ?? $it['nom'] ?? 'Sans nom';
                $prix = $it['prix_unitaire'] ?? $it['prix'] ?? 0;
                $img = !empty($it['image_url']) ? $it['image_url'] : 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?w=200';
            ?>
            <div class="item-card gold-border bg-zinc-900/50 p-3 flex flex-col items-center text-center" 
                 data-cat="<?= $it['categorie'] ?>" 
                 onclick="addToCart('<?= addslashes($nom) ?>', <?= $prix ?>, <?= $it['id'] ?>)">
                <img src="<?= $img ?>" class="w-full h-24 object-cover rounded-xl mb-2 opacity-80">
                <span class="text-[10px] font-bold uppercase truncate w-full"><?= $nom ?></span>
                <span class="gold-text text-xs font-serif"><?= number_format($prix, 0, ',', ' ') ?> F</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="w-96 bg-zinc-900 border-l border-zinc-800 p-6 flex flex-col shadow-2xl">
        <h2 class="text-xl font-serif gold-text italic mb-6 border-b border-zinc-800 pb-2">Note en cours</h2>
        
        <div id="cart-list" class="cart-area space-y-3">
            <p class="text-zinc-600 text-center mt-10 italic">Note vide</p>
        </div>

        <div class="mt-auto space-y-4 pt-4 border-t border-zinc-800">
            <div class="flex justify-between items-end">
                <span class="text-zinc-500 text-xs uppercase">Total</span>
                <span id="total-val" class="text-3xl font-serif gold-text">0 <small class="text-xs">FCFA</small></span>
            </div>

            <select id="chambre_id" class="w-full bg-black border border-zinc-800 p-3 rounded-xl text-xs gold-text outline-none">
                <option value="">-- Paiement Direct (Comptant) --</option>
                <?php foreach($chambres as $ch): ?>
                    <option value="<?= $ch['id'] ?>">Imputer Chambre n°<?= $ch['numero_chambre'] ?></option>
                <?php endforeach; ?>
            </select>

            <div class="grid grid-cols-2 gap-2">
                <button onclick="showQR('Orange Money')" class="bg-orange-600/20 text-orange-500 border border-orange-600/30 py-2 rounded-lg text-[9px] font-bold uppercase">Orange Money</button>
                <button onclick="showQR('Wave')" class="bg-blue-600/20 text-blue-500 border border-blue-600/30 py-2 rounded-lg text-[9px] font-bold uppercase">Wave Money</button>
            </div>

            <button onclick="validateOrder('Espèces')" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-xl uppercase text-xs tracking-widest shadow-lg active:bg-yellow-600">
                Confirmer & Imprimer
            </button>
        </div>
    </div>

    <div id="qr-modal" class="hidden fixed inset-0 bg-black/90 flex items-center justify-center z-50 p-6">
        <div class="bg-white p-6 rounded-3xl text-center max-w-xs">
            <h3 id="qr-title" class="text-black font-bold mb-4 uppercase"></h3>
            <img id="qr-img" src="" class="w-48 h-48 mx-auto mb-4">
            <p class="text-gray-500 text-[10px] mb-4">Scannez pour payer <span id="qr-total"></span> FCFA</p>
            <button onclick="closeQR()" class="bg-black text-white px-6 py-2 rounded-full text-xs">Fermer</button>
        </div>
    </div>

    <div id="ticket-print" class="hidden p-4 bg-white text-black font-mono text-[10px] w-64">
        <center>
            <h2 class="font-bold">PRESTIGE HOTEL</h2>
            <p>CONCIERGERIE / BAR</p>
            <hr class="border-black my-2">
        </center>
        <div id="ticket-items"></div>
        <hr class="border-black my-2">
        <div class="flex justify-between font-bold text-sm">
            <span>TOTAL:</span>
            <span id="ticket-total"></span>
        </div>
        <p class="mt-4 text-center">Merci de votre visite</p>
    </div>

    <script>
        let cart = [];
        let total = 0;

        function filterCat(cat) {
            document.querySelectorAll('.item-card').forEach(el => {
                el.style.display = (cat === 'all' || el.dataset.cat === cat) ? 'flex' : 'none';
            });
        }

        function addToCart(nom, prix, id) {
            cart.push({id, nom, prix});
            render();
        }

        function render() {
            const list = document.getElementById('cart-list');
            list.innerHTML = '';
            total = 0;
            cart.forEach((it, idx) => {
                total += it.prix;
                list.innerHTML += `<div class="flex justify-between items-center bg-zinc-800/40 p-3 rounded-xl text-[11px]">
                    <div><b>${it.nom}</b><br><span class="gold-text">${it.prix.toLocaleString()} F</span></div>
                    <button onclick="cart.splice(${idx},1);render()" class="text-red-500">✕</button>
                </div>`;
            });
            document.getElementById('total-val').innerText = total.toLocaleString() + ' FCFA';
        }

        function showQR(type) {
            if(total === 0) return alert("Note vide");
            document.getElementById('qr-title').innerText = "Paiement " + type;
            document.getElementById('qr-total').innerText = total.toLocaleString();
            // Liens QR réels à remplacer par vos marchands
            document.getElementById('qr-img').src = (type === 'Wave') 
                ? 'https://api.qrserver.com/v1/create-qr-code/?data=WAVE_PAYMENT&size=200x200'
                : 'https://api.qrserver.com/v1/create-qr-code/?data=ORANGE_MONEY_PAYMENT&size=200x200';
            document.getElementById('qr-modal').classList.remove('hidden');
        }

        function closeQR() { document.getElementById('qr-modal').classList.add('hidden'); }

        function validateOrder(mode) {
            if(cart.length === 0) return alert("Sélectionnez des articles");
            
            const data = {
                chambre_id: document.getElementById('chambre_id').value,
                total: total,
                mode_paiement: mode,
                items: cart
            };

            fetch('save_order.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    printTicket();
                    alert("✨ Succès ! Commande enregistrée.");
                    cart = [];
                    render();
                } else { alert("Erreur: " + res.message); }
            });
        }

        function printTicket() {
            const tItems = document.getElementById('ticket-items');
            tItems.innerHTML = '';
            cart.forEach(it => {
                tItems.innerHTML += `<div class="flex justify-between"><span>${it.nom}</span><span>${it.prix}</span></div>`;
            });
            document.getElementById('ticket-total').innerText = total.toLocaleString() + ' F';
            window.print();
        }
    </script>
    
    <?php include '../layout/footer.php'; ?>
</body>
</html>