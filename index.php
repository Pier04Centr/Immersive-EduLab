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
    <title>Campus Gallery</title>
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        <div class="gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px;">
            <?php 
                $where = isset($_GET['category']) ? "WHERE category='".mysqli_real_escape_string($conn, $_GET['category'])."'" : "";
                $res = mysqli_query($conn, "SELECT * FROM gallary $where ORDER BY title ASC");
                while ($row = mysqli_fetch_assoc($res)) {
                    $path = "./uploads/" . $row['path'];
                    $t = htmlspecialchars($row['title']);
                    $c = htmlspecialchars($row['category']);
                    $f = $row['path']; // ID univoco file per la chat
            ?>
                <div class='card' onclick="openViewModal('<?php echo $path; ?>', '<?php echo $t; ?>', '<?php echo $c; ?>', '<?php echo $f; ?>')">
                    <model-viewer src="<?php echo $path; ?>" auto-rotate rotation-per-second="30deg" interaction-prompt="none" disable-zoom shadow-intensity="1" style="pointer-events: none; width:100%; height:260px; background:var(--modal-bg);"></model-viewer>
                    <div class='card-info'>
                        <div style="font-weight:700; font-size:1.1em;"><?php echo $t; ?></div>
                        <div style="font-size:0.9em; margin-top:5px;"><?php echo $c; ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div id="uploadModal" class="modal-overlay" onclick="if(event.target==this) closeUploadModalForce()">
        <div style="background:var(--card-bg); padding:30px; border-radius:15px; width:400px; color:var(--text-main);">
            <h2 style="margin-top:0;">📤 Upload</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Titolo" required style="width:100%; padding:10px; margin-bottom:10px; background:var(--bg); border:1px solid var(--text-sec); color:var(--text-main);">
                <select name="category" style="width:100%; padding:10px; margin-bottom:10px; background:var(--bg); border:1px solid var(--text-sec); color:var(--text-main);">
                    <option>Ingegneria</option><option>Medicina</option><option>Architettura</option><option>Chimica</option>
                </select>
                <input type="file" name="image" accept=".glb,.gltf" required style="margin-bottom:20px;">
                <button type="submit" name="upload" style="width:100%; padding:10px; background:var(--primary); color:white; border:none; border-radius:5px; cursor:pointer;">Carica</button>
            </form>
        </div>
    </div>

    <div id="viewModal" class="modal-overlay" onclick="if(event.target==this) closeViewModalForce()">
        <div class="modal-view-content" onclick="event.stopPropagation()">
            
            <div style="flex-grow: 1; position: relative; background: var(--modal-bg); display:flex; flex-direction:column;">
                <div style="padding: 10px 20px; background:var(--card-bg); display:flex; justify-content:space-between; align-items:center;">
                    <h2 id="modalTitle" style="margin:0; font-size:1.2em;">Titolo</h2>
                    <span id="modalCategory" style="background:var(--bg); padding:2px 8px; border-radius:4px; font-size:0.8em;">Cat</span>
                </div>
                
                <model-viewer id="fullViewer" src="" autoplay loop animation-crossfade-duration="0" ar camera-controls shadow-intensity="1" style="width: 100%; height: 100%;">
                </model-viewer>

                <div id="animControls" style="display:none; position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); width: 80%; background: rgba(255,255,255,0.9); padding: 10px; border-radius: 20px; display:flex; align-items:center; gap:10px;">
                    <button id="playPauseBtn" style="border:none; background:var(--primary); color:white; border-radius:50%; width:30px; height:30px; cursor:pointer;">⏸️</button>
                    <input type="range" id="animSlider" min="0" max="100" value="0" step="0.1" style="flex-grow:1;">
                </div>
            </div>

            <div class="chat-container">
                <div class="chat-header">💬 Commenti</div>
                <div class="chat-messages" id="chatBox">
                    <div style="text-align:center; color:#999; margin-top:20px;">Caricamento...</div>
                </div>
                <div class="chat-input-area">
                    <input type="text" id="chatInput" class="chat-input" placeholder="Scrivi un commento..." onkeypress="if(event.key==='Enter') sendComment()">
                    <button class="chat-send-btn" onclick="sendComment()"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>

        </div>
    </div>

    <script>

        // --- 2. GESTIONE VIEWER E CHAT ---
        const viewer = document.getElementById('fullViewer');
        const slider = document.getElementById('animSlider');
        const playBtn = document.getElementById('playPauseBtn');
        let currentFileRef = ""; // Salva il nome del file corrente per la chat

        function openViewModal(path, title, category, fileRef) {
            document.getElementById('viewModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalCategory').innerText = category;
            
            // Reset Viewer
            viewer.src = path;
            currentFileRef = fileRef; // Importante per la chat!
            
            // Carica i commenti per questo progetto
            loadComments();
        }

        function closeViewModalForce() {
            document.getElementById('viewModal').style.display = 'none';
            viewer.src = "";
        }

        // --- 3. LOGICA CHAT (AJAX) ---
        function loadComments() {
            const chatBox = document.getElementById('chatBox');
            chatBox.innerHTML = "<div style='text-align:center; padding:20px;'>Caricamento...</div>";

            const formData = new FormData();
            formData.append('action', 'load');
            formData.append('path', currentFileRef);

            fetch('api_comments.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                chatBox.innerHTML = "";
                if(data.length === 0) {
                    chatBox.innerHTML = "<div style='text-align:center; color:var(--text-sec); margin-top:20px;'>Nessun commento. Sii il primo!</div>";
                    return;
                }
                data.forEach(msg => {
                    chatBox.innerHTML += `
                        <div class="message">
                            <div class="msg-user">${msg.username}</div>
                            <div class="msg-text">${msg.comment}</div>
                            <div class="msg-time">${msg.created_at}</div>
                        </div>
                    `;
                });
                chatBox.scrollTop = chatBox.scrollHeight; // Scrolldown automatico
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
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    input.value = ""; // Pulisci input
                    loadComments(); // Ricarica commenti
                } else {
                    alert("Errore nell'invio o sessione scaduta.");
                }
            });
        }

        // --- 4. GESTIONE ANIMAZIONE (Slider & Play) ---
        viewer.addEventListener('load', () => {
            const anims = viewer.availableAnimations;
            const controls = document.getElementById('animControls');
            if(anims.length > 0) {
                controls.style.display = 'flex';
                viewer.animationName = anims[0];
                viewer.play();
                loopSlider();
            } else {
                controls.style.display = 'none';
            }
        });

        let isDragging = false;
        function loopSlider() {
            if(!isDragging && !viewer.paused && viewer.duration > 0) {
                slider.value = (viewer.currentTime / viewer.duration) * 100;
            }
            requestAnimationFrame(loopSlider);
        }
        slider.addEventListener('input', (e) => {
            isDragging = true; viewer.pause();
            viewer.currentTime = (viewer.duration * e.target.value) / 100;
        });
        slider.addEventListener('change', () => { isDragging = false; });
        playBtn.addEventListener('click', () => {
            if(viewer.paused) viewer.play(); else viewer.pause();
        });

        // Funzioni modale upload
        function openUploadModal() { document.getElementById('uploadModal').style.display = 'flex'; }
        function closeUploadModalForce() { document.getElementById('uploadModal').style.display = 'none'; }
    </script>
</body>
</html>