<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Récupération - Prestige Hotel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #050505; color: #fff; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border: 1px solid rgba(212, 175, 55, 0.3); }
        .gold-btn { background: linear-gradient(45deg, #D4AF37, #B8860B); color: black; }
    </style>
</head>
<body class="flex items-center justify-center h-screen">
    <div class="glass-card p-10 rounded-3xl shadow-2xl w-full max-w-md text-center">
        <h2 class="text-2xl font-serif text-[#D4AF37] mb-4 uppercase tracking-widest">Récupération</h2>
        <p class="text-gray-400 text-sm mb-8">Saisissez votre email pour réinitialiser votre accès premium.</p>
        
        <form action="reset_request.php" method="POST" class="space-y-6">
            <div class="text-left">
                <label class="block text-xs text-[#D4AF37] uppercase mb-2">Email Professionnel</label>
                <input type="email" name="email" required 
                    class="w-full px-4 py-3 rounded-xl bg-zinc-900 border border-zinc-800 text-white focus:border-[#D4AF37] outline-none">
            </div>
            <button type="submit" class="gold-btn w-full py-4 rounded-xl font-bold hover:scale-105 transition">
                ENVOYER LE LIEN
            </button>
        </form>
        <div class="mt-6">
            <a href="../login.php" class="text-xs text-gray-500 hover:text-[#D4AF37]">Retour à la connexion</a>
        </div>
    


    <p class="text-center mt-10 text-zinc-600 text-[10px] uppercase tracking-tighter">
            &copy; 2026 Bemar Heritage Group. Tous droits réservés.
    </p>

    </div>
</body>
</html>

<!-- La Page de Demande de Récupération -->
<!-- Cette page permet à l'utilisateur de saisir son email pour initier la récupération. -->