<?php
    include("./connect.php"); 
    session_start();
    error_reporting(E_ALL); 

    // Controllo Accesso
    if (!isset($_SESSION['username'])) {
        header("Location: ../login/index.php");
        exit();
    }
    
    // Gestione Messaggi
    $msg = "";
    if (isset($_SESSION['upload_msg'])) {
        $msg = $_SESSION['upload_msg']; unset($_SESSION['upload_msg']); 
    }

    // LOGICA UPLOAD FILE
    if(isset($_POST["upload"])){
        $title = mysqli_real_escape_string($connect, $_POST["title"]);
        $category = mysqli_real_escape_string($connect, $_POST["category"]);
        $filename = $_FILES['image']['name'];
        $tempname = $_FILES['image']['tmp_name'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($fileExt, array('glb', 'gltf'))){
            $newFileName = uniqid("model_", true) . "." . $fileExt;
            if (!file_exists('./uploads')) { mkdir('./uploads', 0777, true); }
            if(move_uploaded_file($tempname, "./uploads/" . $newFileName)){
                $query = "INSERT INTO gallary (title, category, path) VALUES ('$title', '$category', '$newFileName')";
                if(mysqli_query($connect, $query)){
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
    <title>EduLab</title>
    
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

    <div class="sidebar-nav">
        <a href="index.php" class="sidebar-btn" title="Home"><i class="fas fa-home icon"></i></a>
        <a href="#" onclick="openUploadModal(); return false;" class="sidebar-btn" title="Upload"><i class="fas fa-cloud-upload-alt icon"></i></a>
        <a href="../login/personal.php" class="sidebar-btn" title="Profilo"><i class="fas fa-user icon"></i></a>
        
        <button onclick="toggleDarkMode()" class="sidebar-btn" title="Cambia Tema">
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
            <a href="?category=Chimica" class="filter-btn">Chimica</a>
        </div>

        <div class="gallery-grid">
            <?php 
                $where = isset($_GET['category']) ? "WHERE category='".mysqli_real_escape_string($connect, $_GET['category'])."'" : "";
                $res = mysqli_query($connect, "SELECT * FROM gallary $where ORDER BY title ASC");
                while ($row = mysqli_fetch_assoc($res)) {
                    $path = "./uploads/" . $row['path'];
                    $t = htmlspecialchars($row['title']);
                    $c = htmlspecialchars($row['category']);
                    $f = $row['path']; 
            ?>
                <div class='card' onclick="openViewModal('<?php echo $path; ?>', '<?php echo $t; ?>', '<?php echo $c; ?>', '<?php echo $f; ?>')">
                    
                    <model-viewer 
                        src="<?php echo $path; ?>" 
                        loading="lazy" 
                        reveal="auto"
                        interaction-prompt="none" 
                        disable-zoom 
                        shadow-intensity="1" 
                        
                        style="pointer-events: none; width:100%; height:240px; background:var(--modal-bg);">
                    </model-viewer>

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
                        <option>Ingegneria</option>
                        <option>Medicina</option>
                        <option>Chimica</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>File 3D</label>
                    <div class="upload-widget">
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
                
                <div class="floating-title">
                    <h2 id="modalTitle">Titolo</h2>
                </div>

                <button onclick="closeViewModalForce()" style="position:absolute; top:20px; right:20px; z-index:50; background:white; border:none; width:40px; height:40px; border-radius:50%; font-size:24px; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.1); display:flex; align-items:center; justify-content:center;">&times;</button>
                
                <model-viewer 
                    id="fullViewer" 
                    src="" 
                    
                    autoplay loop animation-crossfade-duration="0" 
                    
                    camera-orbit="auto auto 300%"
                    min-camera-orbit="auto auto 100%"
                    max-camera-orbit="auto auto 500%"
                    
                    field-of-view="45deg"
                    
                    ar ar-modes="webxr scene-viewer quick-look" 
                    camera-controls shadow-intensity="1" 
                    style="width: 100%; height: 100%; outline:none;"
                >
                    <button slot="ar-button" class="ar-custom-btn">
                        <i class="fas fa-cube"></i> AR
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
                
                <div class="chat-messages" id="chatBox"></div>
                
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

        // --- 3. VIEWER LOGIC ---
        const viewer = document.getElementById('fullViewer');
        const slider = document.getElementById('animSlider');
        const playBtn = document.getElementById('playPauseBtn');
        const controlsDiv = document.getElementById('animControls');
        let currentFileRef = "";
        let isUserDragging = false;
            
        function openViewModal(path, title, category, fileRef) {
            document.getElementById('viewModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = title;
            // Categoria rimossa dal JS
            
            // Reset
            viewer.src = "";
            slider.value = 0;
            document.getElementById('animPercent').innerText = "0%";
            controlsDiv.style.display = 'none';
            isUserDragging = false;

            // Load
            viewer.src = path;
            currentFileRef = fileRef;
            loadComments();
        }

        function closeViewModalForce() {
            document.getElementById('viewModal').style.display = 'none';
            viewer.src = "";
        }
        
        // --- ANIMAZIONE ---
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
        
        function loopSlider() {
            if(!isUserDragging && !viewer.paused && viewer.duration > 0) {
                const pct = (viewer.currentTime / viewer.duration) * 100;
                slider.value = pct;
                document.getElementById('animPercent').innerText = Math.round(pct) + "%";
            }
            requestAnimationFrame(loopSlider);
        }

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