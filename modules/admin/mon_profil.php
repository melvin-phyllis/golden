<?php
require_once '../../config/db.php';
session_start();

// Sécurité : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les infos complètes
$stmt = $pdo->prepare("SELECT u.*, r.nom_role FROM utilisateurs u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#050505] text-zinc-300 min-h-screen">

    <div class="max-w-4xl mx-auto py-12 px-4">
        <h1 class="text-3xl font-serif text-[#D4AF37] mb-8 uppercase tracking-widest text-center">Espace Collaborateur</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="md:col-span-1 space-y-6">
                <div class="bg-zinc-900/40 p-6 rounded-[2.5rem] border border-zinc-800 text-center">
                    <div class="relative inline-block group">
                        <img src="<?= htmlspecialchars(BASE_URL . ($user['photo'] ?? 'assets/img/default-avatar.png')) ?>" 
                             class="w-32 h-32 rounded-full object-cover border-2 border-[#D4AF37] mb-4 mx-auto">
                        <form action="update_photo.php" method="POST" enctype="multipart/form-data" class="mt-2">
                            <label class="cursor-pointer text-[10px] text-[#D4AF37] uppercase font-bold hover:text-white transition">
                                <i class="fa-solid fa-camera mr-1"></i> Changer la photo
                                <input type="file" name="photo_profile" class="hidden" onchange="this.form.submit()">
                            </label>
                        </form>
                    </div>
                    <h2 class="text-white font-bold mt-4"><?= $user['nom'] ?></h2>
                    <p class="text-[10px] text-zinc-500 uppercase tracking-widest"><?= $user['nom_role'] ?></p>
                </div>

                <a href="change_password.php" class="block w-full text-center bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] uppercase py-3 rounded-xl transition">
                    Sécuriser mon mot de passe
                </a>
            </div>

            <div class="md:col-span-2 space-y-6">
                <div class="bg-zinc-900/40 p-8 rounded-[2.5rem] border border-zinc-800">
                    <h3 class="text-xs font-bold text-[#D4AF37] uppercase mb-6 border-b border-zinc-800 pb-2">Détails Professionnels</h3>
                    
                    <div class="grid grid-cols-2 gap-y-6">
                        <div>
                            <p class="text-[9px] text-zinc-500 uppercase">Email Professionnel</p>
                            <p class="text-sm text-white"><?= $user['email'] ?></p>
                        </div>
                        <div>
                            <p class="text-[9px] text-zinc-500 uppercase">Contact / Téléphone</p>
                            <p class="text-sm text-white"><?= $user['contact'] ?? 'Non renseigné' ?></p>
                        </div>
                        <div>
                            <p class="text-[9px] text-zinc-500 uppercase">Salaire de base</p>
                            <p class="text-sm text-white font-mono"><?= number_format($user['salaire_base'], 0, ',', ' ') ?> CFA</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-zinc-500 uppercase">Planning Actuel</p>
                            <p class="text-sm text-white font-bold text-green-500"><?= $user['planning_horaire'] ?></p>
                        </div>





<div class="mt-8 pt-6 border-t border-zinc-800">
    <p class="text-[9px] text-zinc-500 uppercase mb-4 font-bold tracking-widest">Documents Administratifs</p>
    <a href="generer_fiche_paie.php" class="inline-flex items-center gap-3 bg-zinc-800 hover:bg-[#D4AF37] hover:text-black text-white px-5 py-3 rounded-xl transition text-[10px] uppercase font-bold">
        <i class="fa-solid fa-file-invoice-dollar"></i> Télécharger Fiche de Paie (Février 2026)
    </a>
</div>



                    </div>
                </div>

                <div class="p-6 bg-blue-900/10 border border-blue-500/20 rounded-2xl">
                    <p class="text-[10px] text-blue-400 italic">
                        <i class="fa-solid fa-circle-info mr-2"></i> Les informations de salaire et de planning sont modifiables uniquement par l'Administration.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<!-- Ce fichier permet à l'employé de voir ses informations RH (salaire, planning) et de gérer sa photo. -->