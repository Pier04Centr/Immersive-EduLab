<?php
    include("./connect.php"); 
    session_start();
    error_reporting(E_ALL); 

    if (!isset($_SESSION['username'])) {
        header("Location: ../login/index.php");
        exit();
    }
    
    // Messaggi Upload
    $msg = "";
    if (isset($_SESSION['upload_msg'])) {
        $msg = $_SESSION['upload_msg']; unset($_SESSION['upload_msg']); 
    }

    // LOGICA UPLOAD
    if(isset($_POST["upload"])){
        $title = mysqli_real_escape_string($conn, $_POST["title"]);
        $category = mysqli_real_escape_string($conn, $_POST["category"]);
        $filename = $_FILES['image']['name'];
        $tempname = $_FILES['image']['tmp_name'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($fileExt, array('glb', 'gltf'))){
            $newFileName = uniqid("model_", true) . "." . $fileExt;
            if (!file_exists('./uploads')) { mkdir('./uploads', 0777, true); }
            if(move_uploaded_file($tempname, "./uploads/" . $newFileName)){
                $query = "INSERT INTO gallary (title, category, path) VALUES ('$title', '$category', '$newFileName')";
                if(mysqli_query($conn, $query)){
                    $_SESSION['upload_msg'] = "<div class='alert success' style='background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px; text-align:center;'>✅ Caricato!</div>";
                    header("Location: index.php"); exit();
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Immersive EduLab</title>
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">

    <style>
        /* 1. Placeholder statico per la griglia (Migliora performance) */
        .model-placeholder {
            width: 100%;
            height: 240px;
            background: var(--modal-bg); /* Usa il colore del tema */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-sec);
            transition: background 0.3s;
        }
        .model-placeholder i {
            font-size: 4rem;
            margin-bottom: 10px;
            color: var(--primary);
            transition: transform 0.3s;
        }
        .card:hover .model-placeholder i {
            transform: scale(1.1) rotate(10deg);
        }

        /* 2. Bottone AR Personalizzato (Posizionato in ALTO A SINISTRA) */
        .ar-custom-btn {
            position: absolute;
            top: 20px;
            left: 20px; /* Qui non dà fastidio né alla X né allo slider */
            background-color: white;
            border-radius: 30px;
            border: none;
            padding: 8px 16px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            color: #333;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 50; /* Sopra al modello */
        }
        
        /* Adattamenti Mobile per lo slider */
        @media (max-width: 600px) {
            .controls-floating-bar {
                bottom: 20px; /* Un po' più in basso su mobile */
                width: 95%;   /* Più largo su mobile */
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="sidebar-nav">
        <a href="index.php" class="sidebar-btn" title="Home"><i class="fas fa-home icon"></i></a>
        <a href="#" onclick="openUploadModal(); return false;" class="sidebar-btn" title="Upload"><i class="fas fa-cloud-upload-alt icon"></i></a>
        <a href="../login/personal.php" class="sidebar-btn" title="Profilo"><i class="fas fa-user icon"></i></a>
        
        <button onclick="toggleDarkMode()" class="sidebar-btn" title="Cambia Tema" style="margin-top:auto; margin-bottom:10px;">
            <i id="theme-icon" class="fas fa-moon icon"></i>
        </button>
    </div>

    <div class="container">
        <?php echo $msg; ?>
        <h1 style="text-align:center; margin-bottom: 10px;">Esplora Progetti</h1>
        <p style="text-align:center; margin-bottom: 30px;">Community 3D & Chat</p>
        
        <div class="filters">
            <a href="index.php" class="filter-btn">Tutti</a>
            <a href="?category=Ingegneria" class="filter-btn">Ingegneria</a>
            <a href="?category=Medicina" class="filter-btn">Medicina</a>
            <a href="?category=Architettura" class="filter-btn">Architettura</a>
            <a href="?category=Chimica" class="filter-btn">Chimica</a>
        </div>

        <div class="gallery-grid">
            <?php 
                $where = isset($_GET['category']) ? "WHERE category='".mysqli_real_escape_string($conn, $_GET['category'])."'" : "";
                $res = mysqli_query($conn, "SELECT * FROM gallary $where ORDER BY title ASC");
                while ($row = mysqli_fetch_assoc($res)) {
                    $path = "./uploads/" . $row['path'];
                    $t = htmlspecialchars($row['title']);
                    $c = htmlspecialchars($row['category']);
                    $f = $row['path']; 
            ?>
                <div class='card' onclick="openViewModal('<?php echo $path; ?>', '<?php echo $t; ?>', '<?php echo $c; ?>', '<?php echo $f; ?>')">
                    
                    <div class="model-placeholder">
                        <i class="fas fa-cube"></i>
                        <span style="font-size:0.8em;">Clicca per visualizzare</span>
                    </div>

                    <div class='card-info'>
                        <div style="font-weight:700; font-size:1.1em;"><?php echo $t; ?></div>
                        <div style="font-size:0.9em; margin-top:5px;"><?php echo $c; ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div id="uploadModal" class="modal-overlay" onclick="if(event.target==this) closeUploadModalForce()">
        <div class="modal-upload-content">
            <span class="close-btn" onclick="closeUploadModalForce()">&times;</span>
            <div style="padding: 20px 25px; border-bottom: 1px solid var(--input-border);">
                <h2 style="margin:0;">📤 Nuovo Upload</h2>
            </div>
            <form method="post" enctype="multipart/form-data" style="padding-top:20px;">
                <div class="form-group">
                    <label>Titolo</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Categoria</label>
                    <select name="category">
                        <option>Ingegneria</option><option>Medicina</option><option>Architettura</option><option>Chimica</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>File 3D</label>
                    <div class="upload-anim-box">
                      <div class="folder"><div class="front-side"><div class="tip"></div></div><div class="back-side"></div></div>
                      <label class="custom-file-upload">
                        <input type="file" name="image" accept=".glb,.gltf" required onchange="updateFileName(this)" />
                        <span id="file-label-text">Scegli file .glb</span>
                      </label>
                    </div>
                </div>
                <button type="submit" name="upload" class="btn-submit">Carica Progetto</button>
            </form>
        </div>
    </div>

    <div id="viewModal" class="modal-overlay" onclick="if(event.target==this) closeViewModalForce()">
        <div class="modal-view-content" onclick="event.stopPropagation()">
            
            <div class="viewer-section">
                
                <div style="position:absolute; top:25px; left:30px; z-index:20; pointer-events:none;">
                    <h2 id="modalTitle" style="margin:0; font-size:1.8em; font-weight:800; text-shadow: 0 2px 10px rgba(255,255,255,0.8); color:#333;">Titolo</h2>
                    <span id="modalCategory" style="background:rgba(0,0,0,0.7); color:white; padding:4px 12px; border-radius:20px; font-size:0.85em; font-weight:bold;">Cat</span>
                </div>

                <button onclick="closeViewModalForce()" style="position:absolute; top:20px; right:20px; z-index:50; background:white; border:none; width:40px; height:40px; border-radius:50%; font-size:24px; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center;">&times;</button>
                
                <model-viewer 
                    id="fullViewer" 
                    src="" 
                    autoplay loop animation-crossfade-duration="0" 
                    ar ar-modes="webxr scene-viewer quick-look" 
                    camera-controls shadow-intensity="1" 
                    style="width: 100%; height: 100%; outline:none;"
                >
                    <button slot="ar-button" class="ar-custom-btn">
                        <i class="fas fa-cube"></i> Vedi in AR
                    </button>
                </model-viewer>

                <div id="animControls" class="controls-floating-bar" style="display:none;">
                    <button id="playPauseBtn" class="btn-big-play"><i class="fas fa-pause"></i></button>
                    <div style="flex-grow:1; display:flex; flex-direction:column; justify-content:center;">
                        <div style="display:flex; justify-content:space-between; font-size:0.8em; color:var(--text-sec); margin-bottom:5px; font-weight:600;">
                            <span>Animazione</span>
                            <span id="animPercent">0%</span>
                        </div>
                        <input type="range" id="animSlider" class="styled-slider" min="0" max="100" value="0" step="0.1">
                    </div>
                </div>
            </div>

            <div class="chat-container">
                <div class="chat-header">
                    <span style="display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-comments" style="color:var(--primary);"></i> Community
                    </span>
                </div>
                
                <div class="chat-messages" id="chatBox">
                </div>
                
                <div class="chat-input-area">
                    <input type="text" id="chatInput" class="chat-input" placeholder="Scrivi un messaggio..." onkeypress="if(event.key==='Enter') sendComment()">
                    <button class="chat-send-btn" onclick="sendComment()">
                        <i class="fa-solid fa-share"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        // --- 1. DARK MODE ---
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            document.getElementById('theme-icon').className = isDark ? 'fas fa-sun icon' : 'fas fa-moon icon';
        }
        if (localStorage.getItem('theme') === 'dark') toggleDarkMode();

        // --- 2. UPLOAD UI ---
        function updateFileName(input) {
            const label = document.getElementById('file-label-text');
            if (input.files && input.files.length > 0) label.innerText = "📂 " + input.files[0].name;
        }
        function openUploadModal() { document.getElementById('uploadModal').style.display = 'flex'; }
        function closeUploadModalForce() { document.getElementById('uploadModal').style.display = 'none'; }

        // --- 3. VIEWER & CONTROLLI PRO ---
        const viewer = document.getElementById('fullViewer');
        const slider = document.getElementById('animSlider');
        const playBtn = document.getElementById('playPauseBtn');
        const controlsDiv = document.getElementById('animControls');
        let currentFileRef = "";
        let isUserDragging = false;
            
        function openViewModal(path, title, category, fileRef) {
            document.getElementById('viewModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalCategory').innerText = category;
            
            // Reset
            viewer.src = "";
            slider.value = 0;
            document.getElementById('animPercent').innerText = "0%";
            controlsDiv.style.display = 'none';
            isUserDragging = false;

            // Load (Qui avviene il caricamento vero del 3D)
            viewer.src = path;
            currentFileRef = fileRef;
            loadComments();
        }

        function closeViewModalForce() {
            document.getElementById('viewModal').style.display = 'none';
            viewer.src = "";
        }
        
        // --- GESTIONE ANIMAZIONE ---
        viewer.addEventListener('load', () => {
            const anims = viewer.availableAnimations;
            if(anims.length > 0) {
                controlsDiv.style.display = 'flex';
                viewer.animationName = anims[0];
                viewer.play();
                updatePlayBtnUI(true);
                loopSlider();
            } else {
                controlsDiv.style.display = 'none';
            }
        });
        
        // Loop per muovere slider
        function loopSlider() {
            if(!isUserDragging && !viewer.paused && viewer.duration > 0) {
                const pct = (viewer.currentTime / viewer.duration) * 100;
                slider.value = pct;
                document.getElementById('animPercent').innerText = Math.round(pct) + "%";
            }
            requestAnimationFrame(loopSlider);
        }

        // Interazione Utente
        slider.addEventListener('input', (e) => {
            isUserDragging = true;
            viewer.pause();
            updatePlayBtnUI(false);
            const val = e.target.value;
            viewer.currentTime = (viewer.duration * val) / 100;
            document.getElementById('animPercent').innerText = Math.round(val) + "%";
        });
        
        slider.addEventListener('change', () => { isUserDragging = false; });

        playBtn.addEventListener('click', () => {
            if(viewer.paused) { viewer.play(); updatePlayBtnUI(true); }
            else { viewer.pause(); updatePlayBtnUI(false); }
        });

        function updatePlayBtnUI(isPlaying) {
            if (isPlaying) {
                playBtn.innerHTML = '<i class="fas fa-pause"></i>'; 
                playBtn.style.background = "linear-gradient(135deg, #ff6b6b 0%, #ee5253 100%)";
            } else {
                playBtn.innerHTML = '<i class="fas fa-play" style="margin-left:3px;"></i>';
                playBtn.style.background = "linear-gradient(135deg, #4a90e2 0%, #357abd 100%)";
            }
        }

        // --- 4. CHAT (AJAX) ---
        function loadComments() {
            const chatBox = document.getElementById('chatBox');
            chatBox.innerHTML = "<div style='text-align:center; padding:20px; color:#aaa;'>Caricamento...</div>";

            const formData = new FormData();
            formData.append('action', 'load');
            formData.append('path', currentFileRef);

            fetch('api_comments.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                chatBox.innerHTML = "";
                if(data.length === 0) chatBox.innerHTML = "<div style='text-align:center; color:var(--text-sec); margin-top:30px;'>Nessun commento.</div>";
                data.forEach(msg => {
                    chatBox.innerHTML += `
                        <div class="message">
                            <div class="msg-user">${msg.username}</div>
                            <div class="msg-text">${msg.comment}</div>
                            <div class="msg-time">${msg.created_at}</div>
                        </div>`;
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        }

        function sendComment() {
            const input = document.getElementById('chatInput');
            const text = input.value.trim();
            if(!text) return;

            const formData = new FormData();
            formData.append('action', 'save');
            formData.append('path', currentFileRef);
            formData.append('comment', text);

            fetch('api_comments.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') { input.value = ""; loadComments(); }
            });
        }
    </script>
</body>
</html>