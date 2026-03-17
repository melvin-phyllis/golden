<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@300;400;600&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #050505; color: #a1a1aa; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .sidebar-item:hover { background: rgba(212, 175, 55, 0.1); color: #D4AF37; }
        .active-link { border-right: 3px solid #D4AF37; color: #D4AF37; background: rgba(212, 175, 55, 0.05); }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="w-64 border-r border-zinc-800 flex flex-col fixed h-full bg-[#050505] z-50">
        <div class="p-8">
            
            <h1 class="text-[#D4AF37] font-serif text-2xl tracking-tighter">AU BEMAR </h1>
            <p class="text-[9px] text-zinc-600 uppercase tracking-[0.4em] mt-1"><i>C'est rien que l'amour</i> </p>
        </div>

        <nav class="flex-1 mt-4 px-4 space-y-2">
            <a href="../admin/accueil.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-gauge-high w-5"></i>A La Accueil
            </a>


          <a href="../reception/dashboard.php"
           class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-user-tie w-5 transition-transform duration-300 group-hover:scale-110"></i>
            Réception
            </a>


            <a href="../conciergerie/conciergerie_dash.php"
            class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest transition-all duration-300">
            <i class="fa-solid fa-utensils w-5 transition-transform duration-300 group-hover:scale-110"></i>
             Restaurant & Bar
            </a>


             <a href="../maintenance/gestion_salles.php"
               class="sidebar-item group flex items-center gap-4 px-5 py-3 rounded-2xl text-xs uppercase font-semibold tracking-widest text-gray-400 transition-all duration-300">
                <i class="fa-solid fa-champagne-glasses w-5 group-hover:scale-110 transition"></i>
                Salles d'Événement
            </a>


        
            <a href="../stock/stock_dashboard.php" class="sidebar-item flex items-center gap-4 px-4 py-3 rounded-xl transition text-xs uppercase font-semibold tracking-widest">
                <i class="fa-solid fa-boxes-stacked w-5"></i> Stocks
            </a>
            

        </nav>

        <div class="p-6 border-t border-zinc-900">
            <a href="<?= htmlspecialchars(rtrim(BASE_URL, '/') . '/logout.php') ?>" class="flex items-center gap-4 px-4 py-3 text-red-900 hover:text-red-500 transition text-[10px] uppercase font-bold">
                <i class="fa-solid fa-power-off"></i> Déconnexion
            </a>
        </div>
    </aside>

    <main class="flex-1 ml-64">
        <header class="h-20 border-b border-zinc-800 flex items-center justify-between px-10 bg-[#050505]/80 backdrop-blur-md sticky top-0 z-40">
            <div class="flex items-center gap-4">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] uppercase tracking-widest font-bold text-zinc-500">Système Live</span>
            </div>
            
            <div class="flex items-center gap-6">
                <button class="text-zinc-500 hover:text-white"><i class="fa-regular fa-bell"></i></button>
                <div class="h-8 w-[1px] bg-zinc-800"></div>
                <div class="flex items-center gap-3">
                    <p class="text-xs font-bold text-white uppercase"><?= $_SESSION['nom'] ?? 'Admin' ?></p>
                    <div class="w-8 h-8 rounded-full bg-[#D4AF37] flex items-center justify-center text-black text-xs font-bold">
                        <?= substr($_SESSION['nom'] ?? 'A', 0, 1) ?>
                    </div>
                </div>
            </div>
        </header>
        
        <div class="p-10">