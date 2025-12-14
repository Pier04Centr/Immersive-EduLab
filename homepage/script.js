// CONFIGURAZIONE
const introScreen = document.getElementById('intro-screen');
const bootScreen = document.getElementById('boot-screen');
const mainSite = document.getElementById('main-site');
const outputDiv = document.getElementById('terminal-output');
const asciiIntroDiv = document.getElementById('ascii-intro');

const typeSpeed = 15; 
const glitchDuration = 2500; //2.5 secondi

// ASCII ART PER LA FASE 1
const asciiArt = `
███████╗██████╗ ██╗   ██╗██╗      █████╗ ██████╗ 
██╔════╝██╔══██╗██║   ██║██║     ██╔══██╗██╔══██╗
█████╗  ██║  ██║██║   ██║██║     ███████║██████╔╝
██╔══╝  ██║  ██║██║   ██║██║     ██╔══██║██╔══██╗
███████╗██████╔╝╚██████╔╝███████╗██║  ██║██████╔╝
╚══════╝╚═════╝  ╚═════╝ ╚══════╝╚═╝  ╚═╝╚═════╝ 
`;

// I TUOI LOG PULITI
const bootSequence = [
    { text: "Starting Engine environment...", type: "core" },
    { text: "Loading configuration files...", type: "core" },
    { text: "Mounting Database volume...", type: "warn" },
    { text: "Mounting Assets volume...", type: "warn" },
    { text: "Initializing container...", type: "info" },
    { text: "Starting service...", type: "info" },
    { text: "Verifying network bridge...", type: "ok" },
    { text: "Connection to databases established.", type: "ok" },
    { text: "Loading WebXR Polyfill for AR support...", type: "info" },
    { text: "Initializing model-viewer...", type: "info" },
    { text: "Checking WebGL hardware acceleration...", type: "warn" },
    { text: "GPU Detected: Rendering capabilities active.", type: "ok" },
    { text: "Security check: Auth Module loaded.", type: "ok" },
    { text: "Loading UI Modules...", type: "info" },
    { text: "System ready.", type: "success" },
    { text: "Access Allowed. Welcome to Immersive EduLab!", type: "success" }
];

function getTimestamp() {
    const now = new Date();
    return `[${now.toLocaleTimeString('it-IT')}]`;
}

async function typeLine(text, type) {
    const p = document.createElement('div');
    p.className = `log-line ${type} cursor`;
    const timestampSpan = `<span class="timestamp">${getTimestamp()}</span>`;
    outputDiv.appendChild(p);
    
    let currentHTML = timestampSpan; 
    p.innerHTML = currentHTML + "█"; 

    for (let char of text) {
        currentHTML += char;
        p.innerHTML = currentHTML + '<span style="color:#00f3ff">█</span>';
        window.scrollTo(0, document.body.scrollHeight);
        await new Promise(r => setTimeout(r, Math.random() * typeSpeed));
    }

    p.classList.remove('cursor'); 
    p.innerHTML = currentHTML; 
}

// ORCHESTRAZIONE
async function startSystem() {
    
    // FASE 1: MOSTRA ASCII GLITCH
    asciiIntroDiv.innerText = asciiArt; // Inseriamo il logo

    await new Promise(r => setTimeout(r, glitchDuration));
    
    // FASE 2: TRANSIZIONE AL TERMINALE PULITO
    introScreen.style.opacity = 0; // Fade out logo
    
    await new Promise(r => setTimeout(r, 500)); // Aspetta fade out CSS
    
    introScreen.style.display = 'none'; // Rimuovi intro
    bootScreen.style.display = 'flex';  // Mostra terminale vuoto
    
    // Pausa breve prima che partano i log
    await new Promise(r => setTimeout(r, 400));

    // START LOGS
    for (let line of bootSequence) {
        await typeLine(line.text, line.type);
        await new Promise(r => setTimeout(r, 50 + Math.random() * 80));
    }

    // FASE 3: INGRESSO SITO
    await new Promise(r => setTimeout(r, 1000));
    bootScreen.style.transition = "opacity 0.8s ease";
    bootScreen.style.opacity = 0;
    
    setTimeout(() => {
        bootScreen.style.display = 'none';
        mainSite.style.display = 'flex'; 
    }, 800);
}

window.onload = startSystem;