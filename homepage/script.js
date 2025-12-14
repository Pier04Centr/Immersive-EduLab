const outputDiv = document.getElementById('terminal-output');
const logoDiv = document.getElementById('ascii-logo');
const bootScreen = document.getElementById('boot-screen');
const mainSite = document.getElementById('main-site');

const asciiArt = `
███████╗██████╗ ██╗   ██╗██╗      █████╗ ██████╗ 
██╔════╝██╔══██╗██║   ██║██║     ██╔══██╗██╔══██╗
█████╗  ██║  ██║██║   ██║██║     ███████║██████╔╝
██╔══╝  ██║  ██║██║   ██║██║     ██╔══██║██╔══██╗
███████╗██████╔╝╚██████╔╝███████╗██║  ██║██████╔╝
╚══════╝╚═════╝  ╚═════╝ ╚══════╝╚═╝  ╚═╝╚═════╝ 
`;

// SEQUENZA DI BOOT BASATA SUL TUO PDF
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
    { text: "Welcome.", type: "core" }
];

async function typeLine(text, type, speed = 20) {
    const p = document.createElement('div');
    p.className = `log-line ${type} cursor`;
    outputDiv.appendChild(p);
    
    for (let char of text) {
        p.textContent = p.textContent.replace('█', '') + char + '█';
        window.scrollTo(0, document.body.scrollHeight);
        await new Promise(r => setTimeout(r, Math.random() * speed));
    }
    p.classList.remove('cursor');
    p.textContent = p.textContent.replace('█', ''); // Rimuovi cursore finale
}

async function runBoot() {
    logoDiv.innerText = asciiArt;
    logoDiv.style.opacity = 1;
    await new Promise(r => setTimeout(r, 1000));

    for (let line of bootSequence) {
        await typeLine(`[${new Date().toLocaleTimeString('it-IT')}] ${line.text}`, line.type);
        await new Promise(r => setTimeout(r, 50 + Math.random() * 150));
    }

    await new Promise(r => setTimeout(r, 800));
    
    // Transizione finale
    bootScreen.style.transition = "opacity 0.8s ease";
    bootScreen.style.opacity = 0;
    
    setTimeout(() => {
        bootScreen.style.display = 'none';
        mainSite.style.display = 'flex'; // Flex per centrare
    }, 800);
}

window.onload = runBoot;
