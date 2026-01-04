
// --- 1. GESTIONE SWITCH LOGIN/REGISTER ---
const signinBtn = document.querySelector('.signinBtn');
const signupBtn = document.querySelector('.signupBtn');
const formBx = document.querySelector('.formBx');
const body = document.body;

signupBtn.onclick = function () {
    formBx.classList.add('active');
    body.classList.add('active');
}
signinBtn.onclick = function () {
    formBx.classList.remove('active');
    body.classList.remove('active');
}

// --- 2. GESTIONE TEMA (PERSISTENTE) ---
const themeBtn = document.getElementById('theme-toggle');
const themeIcon = themeBtn.querySelector('i');

// Funzione per applicare il tema
function applyTheme(isLight) {
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

// CONTROLLO MEMORIA ALL'AVVIO
const savedTheme = localStorage.getItem('edulab_theme');
if (savedTheme === 'light') {
    applyTheme(true);
}

// CLICK SUL BOTTONE
themeBtn.addEventListener('click', () => {
    const isCurrentlyLight = document.body.classList.contains('light-mode');
    
    if (isCurrentlyLight) {
        // Passa a Dark
        applyTheme(false);
        localStorage.setItem('edulab_theme', 'dark');
    } else {
        // Passa a Light
        applyTheme(true);
        localStorage.setItem('edulab_theme', 'light');
    }
});

// --- 3. VALIDAZIONE PASSWORD ---
const passwordInput = document.getElementById('regPassword');
const confirmInput = document.getElementById('regConfirmPassword');
const hint = document.getElementById('pwd-hint');
const strongPasswordRegex = /^(?=.*[A-Z])(?=.*[0-9]).{12,}$/;

if(passwordInput) {
    passwordInput.addEventListener('input', function() {
        const val = passwordInput.value;
        hint.style.display = 'block';

        if (strongPasswordRegex.test(val)) {
            passwordInput.classList.remove('invalid');
            passwordInput.classList.add('valid');
            hint.style.color = '#00aa00';
            hint.innerText = "SECURITY: STRONG ✅";
        } else {
            passwordInput.classList.remove('valid');
            passwordInput.classList.add('invalid');
            hint.style.color = '#e74c3c';
            hint.innerText = "SECURITY: WEAK (Req: 12 chars, 1 Upper, 1 Digit)";
        }
    });

    confirmInput.addEventListener('input', function() {
        if (confirmInput.value === passwordInput.value && confirmInput.value !== "") {
            confirmInput.classList.add('valid');
            confirmInput.classList.remove('invalid');
        } else {
            confirmInput.classList.add('invalid');
            confirmInput.classList.remove('valid');
        }
    });
}
