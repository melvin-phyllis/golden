<?php
require_once '../../config/db.php';
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Saisie Finance | Prestige</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#050505] text-white p-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-serif text-[#D4AF37] mb-8 uppercase tracking-widest text-center">Enregistrer une Charge</h1>

        <form action="save_depense.php" method="POST" enctype="multipart/form-data" class="bg-zinc-900/40 p-10 rounded-[2.5rem] border border-zinc-800 space-y-6">
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Catégorie</label>
                    <select name="categorie" id="cat" class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]" onchange="updateSubCat()">
                        <option value="Charges fixes">Charges Fixes</option>
                        <option value="Charges variables">Charges Variables</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Type de dépense</label>
                    <select name="sous_categorie" id="subcat" class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]">
                        </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Montant (CFA)</label>
                    <input type="number" name="montant" required class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37] font-mono">
                </div>
                <div>
                    <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Date de facturation</label>
                    <input type="date" name="date_depense" required class="w-full bg-black border border-zinc-800 p-3 rounded-xl outline-none focus:border-[#D4AF37]">
                </div>
            </div>

            <div>
                <label class="block text-[10px] uppercase text-zinc-500 mb-2 font-bold">Justificatif (Scan/Photo)</label>
                <input type="file" name="justificatif" class="w-full text-xs text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-[#D4AF37] file:text-black hover:file:bg-yellow-600">
            </div>

            <button type="submit" class="w-full bg-[#D4AF37] text-black py-4 rounded-full font-bold uppercase text-[10px] tracking-widest mt-4">
                Valider l'enregistrement financier
            </button>
        </form>
    </div>

    <script>
        const types = {
            "Charges fixes": ["Eau", "Électricité", "Internet", "Canal+", "Salaires", "Sécurité"],
            "Charges variables": ["Nettoyage", "Lessive", "Jardinage", "Achat marché", "Réparations", "Fournitures"]
        };

        function updateSubCat() {
            const cat = document.getElementById('cat').value;
            const sub = document.getElementById('subcat');
            sub.innerHTML = types[cat].map(t => `<option value="${t}">${t}</option>`).join('');
        }
        updateSubCat();
    </script>

    <?php include '../layout/footer.php'; ?>
</body>
</html>





<!-- Ce formulaire permet de classer les charges et de télécharger le scan de la facture -->