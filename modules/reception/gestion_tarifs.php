<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../../login.php');
    exit("Accès refusé");
}
$message = "";

// Traitement de la mise à jour massive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajuster'])) {
    $categorie_id = $_POST['categorie_id'];
    $pourcentage = (float)$_POST['pourcentage']; // ex: 10 pour +10%, -5 pour -5%
    $coefficient = 1 + ($pourcentage / 100);

    try {
        $stmt = $pdo->prepare("UPDATE types_chambre SET tarif_nuit = tarif_nuit * ? WHERE id = ?");
        $stmt->execute([$coefficient, $categorie_id]);
        $message = "Les tarifs ont été ajustés avec succès de $pourcentage%.";
    } catch (PDOException $e) {
        $message = "Erreur : " . $e->getMessage();
    }
}

$categories = $pdo->query("SELECT * FROM types_chambre")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Yield Management | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #050505; color: #fff; font-family: 'Inter', sans-serif; }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .card-luxe { background: linear-gradient(145deg, #111, #0a0a0a); border-radius: 24px; }
    </style>
</head>
<body class="p-10">

    <div class="max-w-4xl mx-auto">
        <div class="mb-12 text-center">
            <h1 class="text-3xl font-serif text-[#D4AF37] uppercase tracking-[0.3em]">Yield Management</h1>
            <p class="text-gray-500 italic mt-2">Optimisation dynamique des revenus de l'établissement</p>
        </div>

        <?php if($message): ?>
            <div class="mb-8 p-4 bg-[#D4AF37]/10 border border-[#D4AF37] text-[#D4AF37] text-center rounded-xl font-bold">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="card-luxe gold-border p-8">
                <h2 class="text-lg font-serif mb-6 uppercase tracking-widest text-gray-300">Ajustement Rapide</h2>
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="text-[10px] uppercase text-gray-500 font-bold block mb-2">Catégorie à modifier</label>
                        <select name="categorie_id" class="w-full bg-zinc-900 border border-zinc-800 p-4 rounded-xl text-white outline-none focus:border-[#D4AF37]">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['libelle']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] uppercase text-gray-500 font-bold block mb-2">Variation (%)</label>
                        <input type="number" name="pourcentage" placeholder="Ex: 10 pour +10% ou -10 pour -10%" required
                               class="w-full bg-zinc-900 border border-zinc-800 p-4 rounded-xl text-white outline-none focus:border-[#D4AF37]">
                    </div>

                    <button type="submit" name="ajuster" class="w-full bg-[#D4AF37] text-black font-bold py-4 rounded-xl uppercase tracking-widest hover:scale-[1.02] transition shadow-lg">
                        Appliquer la Variation
                    </button>
                </form>
            </div>

            <div class="card-luxe gold-border p-8">
                <h2 class="text-lg font-serif mb-6 uppercase tracking-widest text-gray-300">Tarifs Actuels</h2>
                <div class="space-y-4">
                    <?php foreach($categories as $cat): ?>
                        <div class="flex justify-between items-center border-b border-zinc-800 pb-3">
                            <div>
                                <p class="font-bold"><?php echo $cat['libelle']; ?></p>
                                <p class="text-[10px] text-gray-500">Prix de base par nuit</p>
                            </div>
                            <p class="text-[#D4AF37] font-mono"><?php echo number_format($cat['tarif_nuit'], 0, ',', ' '); ?> FCFA</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>


    <?php include '../layout/footer.php'; ?>

</body>
</html>




<!-- La Gestion des Tarifs Dynamiques et des Saisons. -->