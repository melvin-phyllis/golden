<footer class="bg-[#030303] border-t border-zinc-900 pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-10 grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
        
        <div class="space-y-4">
            <h3 class="text-[#D4AF37] font-serif text-xl uppercase tracking-widest">Bémar Prestige</h3>
            <p class="text-xs leading-relaxed text-zinc-500 italic">
                "C'est rien que l'amour" — L'excellence hôtelière au cœur d'Abidjan, 
                alliant tradition africaine et luxe contemporain.
            </p>
            <div class="flex gap-4 pt-4">
                <a href="https://chat.openai.com" target="_blank" title="ChatGPT" class="w-8 h-8 rounded-full border border-zinc-800 flex items-center justify-center text-zinc-500 hover:border-[#D4AF37] hover:text-[#D4AF37] transition duration-300">
                    <i class="fa-solid fa-robot text-xs"></i>
                </a>
                <a href="https://wa.me/22500000000" target="_blank" title="WhatsApp" class="w-8 h-8 rounded-full border border-zinc-800 flex items-center justify-center text-zinc-500 hover:border-green-500 hover:text-green-500 transition duration-300">
                    <i class="fa-brands fa-whatsapp text-xs"></i>
                </a>
                <a href="https://youtube.com" target="_blank" title="YouTube" class="w-8 h-8 rounded-full border border-zinc-800 flex items-center justify-center text-zinc-500 hover:border-red-600 hover:text-red-600 transition duration-300">
                    <i class="fa-brands fa-youtube text-xs"></i>
                </a>
                <a href="https://google.com" target="_blank" title="Google" class="w-8 h-8 rounded-full border border-zinc-800 flex items-center justify-center text-zinc-500 hover:border-blue-500 hover:text-blue-500 transition duration-300">
                    <i class="fa-brands fa-google text-xs"></i>
                </a>
                <a href="https://instagram.com" target="_blank" title="Instagram" class="w-8 h-8 rounded-full border border-zinc-800 flex items-center justify-center text-zinc-500 hover:border-pink-500 hover:text-pink-500 transition duration-300">
                    <i class="fa-brands fa-instagram text-xs"></i>
                </a>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <h4 class="text-white text-[10px] font-black uppercase tracking-[0.2em] mb-2">Navigation</h4>
            <a href="../reception/dashboard.php" class="text-xs text-zinc-500 hover:text-[#D4AF37] transition">Réception</a>
            <a href="../conciergerie/conciergerie_dash.php" class="text-xs text-zinc-500 hover:text-[#D4AF37] transition">Conciergerie</a>
            <a href="../admin/dashboard.php" class="text-xs text-zinc-500 hover:text-[#D4AF37] transition">Administration</a>
            <a href="../finances/comptable.php" class="text-xs text-zinc-500 hover:text-[#D4AF37] transition">Comptabilité</a>
        </div>

        <div class="flex flex-col gap-3">
            <h4 class="text-white text-[10px] font-black uppercase tracking-[0.2em] mb-2">Ressources</h4>
            <a href="https://www.booking.com" target="_blank" class="text-xs text-zinc-500 hover:text-[#D4AF37] transition italic">Extranet Booking</a>
            <a href="https://www.expediapartnercentral.com" target="_blank" class="text-xs text-zinc-500 hover:text-[#D4AF37] transition italic">Expedia Central</a>
            <a href="#" class="text-xs text-zinc-500 hover:text-[#D4AF37] transition">Manuel Utilisateur (PDF)</a>
            <div class="pt-2">
                <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="text-[9px] uppercase font-bold text-[#D4AF37] flex items-center gap-2">
                    <i class="fa-solid fa-arrow-up"></i> Retour en haut
                </button>
            </div>
        </div>

        <div class="space-y-4">
            <h4 class="text-white text-[10px] font-black uppercase tracking-[0.2em] mb-2">Support Système</h4>
            <div class="flex items-center gap-3 text-xs text-zinc-400">
                <i class="fa-solid fa-headset text-[#D4AF37]"></i>
                <span>Assistance Technique : Int. 104</span>
            </div>
            <div class="p-4 bg-zinc-900/50 rounded-2xl border border-zinc-800 backdrop-blur-sm">
                <p class="text-[9px] text-zinc-600 uppercase font-bold mb-1">Infrastructure</p>
                <div class="flex items-center justify-between">
                    <p class="text-[10px] text-green-500 font-mono italic">Server: Online</p>
                    <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                </div>
            </div>
        </div>


        
    </div>

    <div class="text-center border-t border-zinc-900/50 pt-8 px-10 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex gap-6">

        <p class="text-[9px] text-zinc-700 uppercase tracking-[0.5em]">
            &copy; <?= date('Y') ?> Golden PMS - Propriété exclusive du Bémar Prestige
        </p>
            <span class="text-[9px] text-zinc-800 uppercase tracking-widest">Version 3.1.0-Flash</span>
            <span class="text-[9px] text-zinc-800 uppercase tracking-widest italic">Abidjan, Côte d'Ivoire</span>
        </div>
    </div>
    
</footer>