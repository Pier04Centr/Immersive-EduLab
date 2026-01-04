<?php
session_start();
include 'conn.php'; // Assicurati che il percorso sia giusto

// 1. Controllo sessione
if (!isset($_SESSION['username'])) {
    header("Location: ./index.php");
    exit();
}

// 2. Controllo connessione DB
if (!$conn) {
    die("Errore di connessione al database.");
}

// 3. RECUPERO DATI
$username_corrente = mysqli_real_escape_string($conn, $_SESSION['username']);
$sql = "SELECT * FROM utenti WHERE Username = '$username_corrente'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $currentUser = mysqli_fetch_assoc($result);
} else {
    session_destroy();
    header("Location: ./index.php");
    exit();
}
if ($currentUser['Admin'] == 1) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo - <?php echo htmlspecialchars($currentUser['Username']); ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://mdbootstrap.com/api/snippets/static/download/MDB5-Free_6.1.0/css/mdb.min.css">
    
    <link rel="stylesheet" href="../Image-Gallary/style.css">
    <link rel="stylesheet" href="personal.css">
</head>

<body>

    <div class="sidebar-nav">
        <a href="../Image-Gallary/index.php" class="sidebar-btn" title="Torna alla Gallery">
            <i class="fas fa-home icon"></i>
        </a>
        
        <a href="./logout.php" class="sidebar-btn" title="Logout">
            <i class="fas fa-sign-out-alt icon"></i>
        </a>

        <a href="" class="sidebar-btn" title="Il tuo Profilo" >
            <i class="fas fa-user icon"></i>
        </a>

        <button onclick="toggleDarkMode()" class="sidebar-btn" title="Cambia Tema" id="theme-toggle">
            <i id="theme-icon" class="fas fa-moon icon"></i>
        </button>
    </div>

    <div class="container" style="padding-bottom: 50px;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card card-profile">
                    <div class="card-body text-center p-5">
                        
                        <div id="avatar-area" style="width: 100%; height: 300px; position: relative; display: flex; justify-content: center; align-items: center; margin-bottom: 20px;">
                            
                            <?php if (!empty($currentUser['avatar_3d'])): ?>
                                <div id="avatar-container" style="width: 300px; height: 300px; cursor: move;"></div>
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?= strtoupper(substr($currentUser['Nome'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>

                        </div>
                        
                        <h4 class="mb-2">
                            <?= htmlspecialchars(ucfirst($currentUser['Nome']) . " " . ucfirst($currentUser['Cognome'])) ?>
                        </h4>
                        
                        <div class="badge-role">
                            <?= htmlspecialchars($currentUser['Descrizione'] ?? 'Membro Community') ?>
                        </div>
                        
                        <div class="list-group list-group-flush text-start">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-user me-3"></i>Username</span>
                                <span class="fw-bold"><?= htmlspecialchars($currentUser['Username']) ?></span>
                            </div>
                            
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-envelope me-3"></i>Email</span>
                                <span><?= htmlspecialchars($currentUser['Email'] ?? 'N/D') ?></span>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-calendar-alt me-3"></i>Iscritto dal</span>
                                <span>2024</span> </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            document.getElementById('theme-icon').className = isDark ? 'fas fa-sun icon' : 'fas fa-moon icon';
        }

        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            document.getElementById('theme-icon').className = 'fas fa-sun icon';
        }
    </script>

    <?php if (!empty($currentUser['avatar_3d'])): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

    <script>
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
                // Ruota il modello di 90 gradi (Math.PI / 2) o -90 gradi (-Math.PI / 2)
                // Se guarda a destra, usa Math.PI / 2. Se guarda a sinistra, usa -Math.PI / 2.
                // Proviamo con circa 45-90 gradi per farli guardare dritti:
                model.rotation.y = - Math.PI / 1.98; // Ruota di 90 gradi verso sinistra
                //ruota verso il basso se necessario
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
    </script>
    <?php endif; ?>
</body>
</html>