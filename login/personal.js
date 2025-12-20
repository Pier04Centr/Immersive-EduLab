document.addEventListener("DOMContentLoaded", function() {
    // Ricostruiamo il percorso completo: Cartella + Nome File dal DB
    const avatarFilename = "<?php echo $currentUser['avatar_3d']; ?>";
    const avatarPath = "./avatars/" + avatarFilename;

    init3DViewer(avatarPath);
});

function init3DViewer(url) {
    const container = document.getElementById('avatar-container');
    
    // Scena e Camera
    const scene = new THREE.Scene();
    // Zoom della camera regolato per vedere bene la testa
    const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
    camera.position.set(0, 0, 3.5);

    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    container.appendChild(renderer.domElement);

    // Luci Cyberpunk
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const blueLight = new THREE.PointLight(0x00f3ff, 2, 50);
    blueLight.position.set(-2, 2, 2);
    scene.add(blueLight);

    const yellowLight = new THREE.PointLight(0xffcc00, 2, 50);
    yellowLight.position.set(2, 2, 2);
    scene.add(yellowLight);

    // Caricamento Modello
    const loader = new THREE.GLTFLoader();
    let modelGroup = new THREE.Group();
    scene.add(modelGroup);

    loader.load(url, (gltf) => {
        const model = gltf.scene;

        // Auto-Centramento (Fondamentale per modelli scaricati da internet)
        const box = new THREE.Box3().setFromObject(model);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        model.position.x = -center.x;
        model.position.y = -center.y;
        model.position.z = -center.z;
        
        modelGroup.add(model);

        // Auto-Zoom (Scala il modello per farlo stare nel box)
        const maxDim = Math.max(size.x, size.y, size.z);
        const scaleFactor = 2.2 / maxDim; 
        modelGroup.scale.set(scaleFactor, scaleFactor, scaleFactor);

        // --- 2. NUOVA CORREZIONE ROTAZIONE (IL FIX) ---
        model.rotation.y = - Math.PI / 1.98; // Ruota di 90 gradi verso sinistra
        model.rotation.x = Math.PI / 15; // Leggera inclinazione di 10 gradi verso il basso  

        // Animazione Idle (se presente)
        if (gltf.animations && gltf.animations.length > 0) {
            const mixer = new THREE.AnimationMixer(model);
            const clip = gltf.animations[0];
            mixer.clipAction(clip).play();
            
            const clock = new THREE.Clock();
            function animateLoop() {
                requestAnimationFrame(animateLoop);
                mixer.update(clock.getDelta());
            }
            animateLoop();
        }
    }, undefined, function(error) {
        console.error("Errore caricamento avatar:", error);
        container.innerHTML = "<p style='color:red;'>Errore visualizzazione 3D</p>";
    });

    // Effetto Segui-Mouse
    document.addEventListener('mousemove', (e) => {
        const mouseX = (e.clientX / window.innerWidth) * 2 - 1;
        const mouseY = (e.clientY / window.innerHeight) * 2 - 1;
        
        // Rotazione fluida
        modelGroup.rotation.y = mouseX * 0.6; // Destra/Sinistra
        modelGroup.rotation.x = mouseY * 0.3; // Su/Giù
    });

    // Loop di render
    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }
    animate();
    
    // Gestione resize finestra
    window.addEventListener('resize', () => {
        const width = container.clientWidth;
        const height = container.clientHeight;
        renderer.setSize(width, height);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
    });
}