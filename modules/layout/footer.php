</div> </main>

    <footer class="fixed bottom-0 right-0 p-6 text-[9px] uppercase tracking-[0.3em] text-zinc-700 bg-transparent pointer-events-none">
        &copy; 2026   BEMAR HOTEL PRESTIGE  & SPA • Powered by Gemini Systems
    </footer>

    <script>
        // Script pour marquer le lien actif
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sidebar-item').forEach(link => {
            if(currentPath.includes(link.getAttribute('href').split('/').pop())) {
                link.classList.add('active-link');
            }
        });
    </script>
</body>
</html>