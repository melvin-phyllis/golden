<?php
require_once '../../config/db.php';
session_start();

// 1. SÉCURITÉ (Décommenter quand tu seras prêt)
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
//     header('Location: ../../login.php');
//     exit("Accès refusé");
// }

$message = "";
$status = "";

// 2. RÉCUPÉRATION DES RÔLES (Correction du nom de variable)
$roles = $pdo->query("SELECT * FROM roles ORDER BY nom_role ASC")->fetchAll();

// 3. LOGIQUE D'INSCRIPTION
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    // On récupère 'password' (nom du champ HTML)
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    // On récupère 'role_id' (nom du champ HTML)
    $role = $_POST['role_id']; 
    $salaire = $_POST['salaire_base'] ?? 0;
    $planning = $_POST['planning_horaire'] ?? 'Non défini';

    try {
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            $message = "Cet email est déjà utilisé.";
            $status = "error";
        } else {
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role, salaire_base, planning_horaire) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $email, $password, $role, $salaire, $planning]);
            $message = "Le compte de $nom a été créé avec succès !";
            $status = "success";
        }
    } catch (PDOException $e) {
        $message = "Erreur système : " . $e->getMessage();
        $status = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recrutement Collaborateur | Prestige Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: radial-gradient(circle at center, #111 0%, #000 100%); }
        .gold-border { border: 1px solid rgba(212, 175, 55, 0.3); }
        .input-style { 
            background: rgba(0,0,0,0.5); 
            border: 1px solid #27272a; 
            border-radius: 1rem; 
            padding: 1rem; 
            font-size: 0.875rem; 
            color: white; 
            transition: all 0.3s;
        }
        .input-style:focus { border-color: #D4AF37; outline: none; }
    </style>
</head>
<body class="min-h-screen p-6 flex flex-col items-center">

    <div class="w-full max-w-4xl">
        <div class="flex justify-between items-center mb-10">
            <div>
                <a href="users_list.php" class="text-zinc-500 text-xs uppercase tracking-widest hover:text-[#D4AF37]">← Retour à la liste</a>
                <h1 class="text-3xl font-serif text-[#D4AF37] mt-4 uppercase">Recruter un Collaborateur</h1>
            </div>
            <div class="text-right">
                <span class="text-zinc-600 text-[9px] uppercase tracking-widest block">Session Admin</span>
                <span class="text-white text-xs font-bold"><?= $_SESSION['nom'] ?? 'Administrateur' ?></span>
            </div>
        </div>

        <div class="bg-zinc-900/50 backdrop-blur-xl p-8 rounded-[2.5rem] gold-border shadow-2xl">
            <?php if($message): ?>
                <div class="<?= $status == 'success' ? 'bg-green-500/10 border-green-500 text-green-500' : 'bg-red-500/10 border-red-500 text-red-500' ?> border p-4 rounded-2xl text-xs mb-8 text-center animate-pulse">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <h2 class="text-[10px] font-bold uppercase text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-2">Identifiants</h2>
                        
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2 ml-2">Nom Complet</label>
                            <input type="text" name="nom" required placeholder="Ex: Jean Marc" class="input-style w-full">
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2 ml-2">Email (Login)</label>
                            <input type="email" name="email" required placeholder="staff@prestige.com" class="input-style w-full">
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-zinc-500 mb-2 ml-2">Mot de passe provisoire</label>
                            <input type="password" name="password" required placeholder="••••••••" class="input-style w-full">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <h2 class="text-[10px] font-bold uppercase text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-2">Détails du Poste</h2>

                        <div>
                            <label class="block text-xs mb-2 text-zinc-400">Rôle / Fonction</label>
                            <select name="role_id" class="input-style w-full text-[#D4AF37] appearance-none">
                                <option value="">Choisir un rôle...</option>
                                <?php foreach($roles as $r): ?>
                                    <option value="<?= $r['nom_role'] ?>"><?= $r['nom_role'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs mb-2 text-zinc-400">Salaire de Base (CFA)</label>
                            <input type="number" name="salaire_base" placeholder="ex: 250000" class="input-style w-full font-mono">
                        </div>

                        <div>
                            <label class="block text-xs mb-2 text-zinc-400">Planning Horaire</label>
                            <input type="text" name="planning_horaire" placeholder="ex: Lun-Ven (08h-17h)" class="input-style w-full">
                        </div>
                    </div>
                </div>

                <div class="mt-12 flex justify-end">
                    <button type="submit" class="bg-[#D4AF37] text-black px-12 py-4 rounded-full font-bold uppercase text-[10px] tracking-widest hover:bg-yellow-600 transition shadow-xl">
                        Créer le compte collaborateur
                    </button>
                </div>
            </form>
        </div>


        <p class="text-center mt-8 text-zinc-600 text-[10px] uppercase tracking-widest">
            Déjà inscrit ? <a href="login.php" class="text-[#D4AF37] hover:underline">Connexion</a>
        </p>

        <p class="text-center mt-10 text-zinc-600 text-[10px] uppercase tracking-tighter">
            &copy; 2026 Bemar Heritage Group. Tous droits réservés.
        </p>
    </div>
</body>
</html>