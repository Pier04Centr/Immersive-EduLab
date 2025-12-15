document.addEventListener("DOMContentLoaded", function() {
    
    // Seleziona il bottone e l'icona al suo interno
    const themeBtn = document.getElementById('theme-toggle');
    
    // Se il bottone non esiste (errore HTML), fermiamo lo script per evitare errori in console
    if (!themeBtn) return;

    const themeIcon = themeBtn.querySelector('i');

    // Funzione per aggiornare l'interfaccia
    function setThemeUI(isDark) {
        if (isDark) {
            // ATTIVA DARK MODE
            document.body.classList.remove('light-mode');
            document.body.classList.add('dark-mode'); // Aggiungiamo anche questo per compatibilità con personal.css
            
            // Icona diventa SOLE (per poter tornare alla luce)
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            // ATTIVA LIGHT MODE
            document.body.classList.add('light-mode');
            document.body.classList.remove('dark-mode');
            
            // Icona diventa LUNA (per poter tornare al buio)
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }

    // 1. Controllo Memoria (Uso 'theme' per compatibilità con personal.php)
    const savedTheme = localStorage.getItem('theme');
    
    // Se salvato 'light', attiva light. Altrimenti default dark.
    if (savedTheme === 'light') {
        setThemeUI(false); // isDark = false
    } else {
        setThemeUI(true);  // isDark = true
    }

    // 2. Evento Click
    themeBtn.addEventListener('click', () => {
        // Controlla se siamo in light mode
        const isCurrentlyLight = document.body.classList.contains('light-mode');
        
        if (isCurrentlyLight) {
            // Passa a Dark
            setThemeUI(true);
            localStorage.setItem('theme', 'dark');
        } else {
            // Passa a Light
            setThemeUI(false);
            localStorage.setItem('theme', 'light');
        }
    });
});