
<div class="mt-12 pt-8 border-t border-zinc-900 text-center">
    <p class="text-[10px] text-zinc-500 uppercase tracking-widest mb-4">Nouveau collaborateur ?</p>
    
    <a href="https://wa.me/2250000000000?text=Bonjour%20Admin,%20je%20suis%20un%20nouveau%20collaborateur%20au%20Bémar%20Prestige%20et%20je%20souhaite%20obtenir%20mes%20accès%20de%20connexion." 
       target="_blank"
       class="inline-flex items-center gap-3 px-6 py-3 bg-zinc-900 border border-zinc-800 rounded-2xl hover:border-[#D4AF37] hover:text-[#D4AF37] transition group">
        <i class="fa-brands fa-whatsapp text-green-500 text-lg group-hover:scale-110 transition"></i>
        <span class="text-xs font-bold uppercase tracking-tighter">Contacter l'administrateur</span>
    </a>
</div>


<p class="mt-6 text-center text-zinc-500 text-[10px] uppercase tracking-widest">
    Pas encore de compte ? 
    <button onclick="toggleRequestModal()" class="text-[#D4AF37] font-bold hover:underline">
        Faire une demande d'accès
    </button>
</p>

<div id="requestModal" class="hidden fixed inset-0 bg-black/90 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] w-full max-w-sm shadow-2xl">
        <h3 class="text-[#D4AF37] font-serif text-xl mb-4 uppercase">Demande d'accès</h3>
        <p class="text-zinc-500 text-[10px] mb-6 leading-relaxed">
            Remplissez vos informations. L'administrateur créera votre compte après vérification.
        </p>
        
        <form action="send_request.php" method="POST" class="space-y-4">
            <input type="text" name="nom_demande" placeholder="Nom complet" required 
                   class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-[#D4AF37]">
            
            <input type="email" name="email_demande" placeholder="Votre email" required 
                   class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-[#D4AF37]">
            
            <select name="poste_souhaite" class="w-full bg-black border border-zinc-800 rounded-xl px-4 py-3 text-xs text-[#D4AF37] outline-none">
                <option value="Réception">Réception</option>
                <option value="Comptabilité">Comptabilité</option>
                <option value="Gouvernance">Gouvernance</option>
            </select>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleRequestModal()" class="flex-1 py-3 text-[10px] uppercase font-bold text-zinc-500">Annuler</button>
                <button type="submit" class="flex-1 py-3 bg-[#D4AF37] text-black text-[10px] uppercase font-black rounded-xl hover:bg-yellow-600 transition">Envoyer</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRequestModal() {
    const modal = document.getElementById('requestModal');
    modal.classList.toggle('hidden');
}
</script>