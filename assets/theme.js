(function() {
    // Vérifier le thème enregistré ou la préférence système
    function applyTheme() {
        if (localStorage.theme === 'dark' || 
            (!('theme' in localStorage) && 
             window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
    
    // Appliquer immédiatement
    applyTheme();
    
    // Réappliquer à chaque navigation Turbo
    document.addEventListener('turbo:load', applyTheme);
    
    // Créer un système d'événements pour synchroniser tous les composants
    document.addEventListener('themeChanged', function(e) {
        localStorage.theme = e.detail.theme;
        applyTheme();
    });
})();