
document.addEventListener("DOMContentLoaded", function() {
    
    const themeBtn = document.getElementById('theme-toggle');
    const themeIcon = themeBtn.querySelector('i');

    function setThemeUI(isLight) {
        if (isLight) {
            document.body.classList.add('light-mode');
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        } else {
            document.body.classList.remove('light-mode');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }
    }

    // Memoria
    const savedTheme = localStorage.getItem('edulab_theme');
    if (savedTheme === 'light') {
        setThemeUI(true);
    } else {
        setThemeUI(false);
    }

    // Toggle
    themeBtn.addEventListener('click', () => {
        const isCurrentlyLight = document.body.classList.contains('light-mode');
        if (isCurrentlyLight) {
            setThemeUI(false);
            localStorage.setItem('edulab_theme', 'dark');
        } else {
            setThemeUI(true);
            localStorage.setItem('edulab_theme', 'light');
        }
    });
});
