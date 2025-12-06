<?php
    // --- 1. CONFIGURAZIONE E LOGICA BACKEND ---
    include("./connect.php"); 
    session_start();
    error_reporting(E_ALL); 

    // Controllo Login
    if (!isset($_SESSION['username'])) {
        header("Location: ../login/index.php");
        exit();
    }

    $nome_utente = ucfirst($_SESSION['nome'] ?? 'Utente'); 
    
    // GESTIONE MESSAGGI (Pattern PRG)
    $msg = "";
    if (isset($_SESSION['upload_msg'])) {
        $msg = $_SESSION['upload_msg']; 
        unset($_SESSION['upload_msg']); 
    }

    // LOGICA UPLOAD FILE
    if(isset($_POST["upload"])){
        $title = mysqli_real_escape_string($conn, $_POST["title"]);
        $category = mysqli_real_escape_string($conn, $_POST["category"]);
        
        $filename = $_FILES['image']['name'];
        $tempname = $_FILES['image']['tmp_name'];
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = array('glb', 'gltf'); 

        if(in_array($fileExt, $allowed)){
            // Nome file univoco
            $newFileName = uniqid("model_", true) . "." . $fileExt;
            $folder = "./uploads/" . $newFileName; 

            if (!file_exists('./uploads')) { mkdir('./uploads', 0777, true); }

            if(move_uploaded_file($tempname, $folder)){
                // QUERY MODIFICATA: Non usa più ID se non c'è nel DB
                $query = "INSERT INTO gallary (title, category, path) VALUES ('$title', '$category', '$newFileName')";
                if(mysqli_query($conn, $query)){
                    $_SESSION['upload_msg'] = "<div class='alert success'>✅ Modello caricato con successo!</div>";
                    header("Location: index.php"); 
                    exit();
                } else {
                    $msg = "<div class='alert error'>❌ Errore Database: " . mysqli_error($conn) . "</div>";
                }
            } else {
                $msg = "<div class='alert error'>❌ Errore nello spostamento del file</div>";
            }
        } else {
            $msg = "<div class='alert error'>⚠️ Formato non valido! Usa solo .glb</div>";
        }
    }
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenAR Campus - Gallery</title>
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar-nav">
        <a href="./index.php" class="sidebar-btn" title="Home">
            <svg class="icon" viewBox="0 0 1024 1024" fill="currentColor" height="1em" width="1em">
                <path d="M946.5 505L560.1 118.8l-25.9-25.9a31.5 31.5 0 0 0-44.4 0L77.5 505a63.9 63.9 0 0 0-18.8 46c.4 35.2 29.7 63.3 64.9 63.3h42.5V940h691.8V614.3h43.4c17.1 0 33.2-6.7 45.3-18.8a63.6 63.6 0 0 0 18.7-45.3c0-17-6.7-33.1-18.8-45.2zM568 868H456V664h112v204zm217.9-325.7V868H632V640c0-22.1-17.9-40-40-40H432c-22.1 0-40 17.9-40 40v228H238.1V542.3h-96l370-369.7 23.1 23.1L882 542.3h-96.1z"></path>
            </svg>
        </a>

        <a href="../login/personal.php" class="sidebar-btn" title="Area Personale">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor" height="1em" width="1em">
                <path d="M12 2.5a5.5 5.5 0 0 1 3.096 10.047 9.005 9.005 0 0 1 5.9 8.181.75.75 0 1 1-1.499.044 7.5 7.5 0 0 0-14.993 0 .75.75 0 0 1-1.5-.045 9.005 9.005 0 0 1 5.9-8.18A5.5 5.5 0 0 1 12 2.5ZM8 8a4 4 0 1 0 8 0 4 4 0 0 0-8 0Z"></path>
            </svg>
        </a>

        <a href="../login/logout.php" class="sidebar-btn" title="Logout">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </a>
    </div>

    <div class="container">
        
        <?php echo $msg; ?>

        <section>
            <h1 style="text-align:center; margin-bottom: 10px; color:#444;">Esplora Progetti</h1>
            <p style="text-align:center; color:#777; margin-bottom: 30px;">Scopri i modelli condivisi dalla community</p>
            
            <div class="filters">
                <a href="index.php" class="filter-btn">Tutti</a>
                <a href="?category=Ingegneria" class="filter-btn">Ingegneria</a>
                <a href="?category=Medicina" class="filter-btn">Medicina</a>
                <a href="?category=Architettura" class="filter-btn">Architettura</a>
                <a href="?category=Chimica" class="filter-btn">Chimica</a>
            </div>

            <div class="gallery-grid">
                <?php 
                    $whereClause = "";
                    if(isset($_GET['category']) && !empty($_GET['category'])) {
                        $cat = mysqli_real_escape_string($conn, $_GET['category']);
                        $whereClause = "WHERE category='$cat'";
                    }

                    // --- QUERY CORRETTA: ORDER BY title ASC ---
                    $query = "SELECT * FROM gallary $whereClause ORDER BY title ASC";
                    $data = mysqli_query($conn, $query);

                    if (mysqli_num_rows($data) > 0) {
                        while ($res = mysqli_fetch_assoc($data)) {
                            $fullPath = "./uploads/" . $res['path'];
                            $jsTitle = htmlspecialchars($res['title'], ENT_QUOTES);
                            $jsCat = htmlspecialchars($res['category'], ENT_QUOTES);
                ?>
                            <div class='card' onclick="openViewModal('<?php echo $fullPath; ?>', '<?php echo $jsTitle; ?>', '<?php echo $jsCat; ?>')">
                                <model-viewer 
                                    src="<?php echo $fullPath; ?>" 
                                    auto-rotate rotation-per-second="30deg"
                                    interaction-prompt="none" disable-zoom shadow-intensity="1"
                                    style="pointer-events: none;">
                                </model-viewer>
                                <div class='card-info'>
                                    <div style="font-weight:700; font-size:1.1em;"><?php echo $res['title']; ?></div>
                                    <div style="color: #888; font-size:0.9em; margin-top:5px;"><?php echo $res['category']; ?></div>
                                </div>
                            </div>
                <?php 
                        }
                    } else {
                        echo "<p style='text-align:center; grid-column: 1/-1; padding:30px; color:#777;'>Nessun progetto trovato.</p>";
                    }
                ?>
            </div>
        </section>
    </div>

    <div id="uploadModal" class="modal-overlay" onclick="closeUploadModal(event)">
        <div class="modal-upload-content" onclick="event.stopPropagation()">
            <span class="close-btn" onclick="closeUploadModalForce()">&times;</span>
            
            <div style="padding: 20px 25px; background: #fafafa; border-bottom: 1px solid #eee;">
                <h2 style="margin:0; color:#333;">📤 Nuovo Upload</h2>
            </div>
            
            <form action="" method="post" enctype="multipart/form-data" style="padding-top:20px;">
                <div class="form-group">
                    <label>Titolo Progetto</label>
                    <input type="text" name="title" placeholder="Es. Motore V8" required>
                </div>
                <div class="form-group">
                    <label>Categoria</label>
                    <select name="category">
                        <option value="Ingegneria">Ingegneria</option>
                        <option value="Medicina">Medicina</option>
                        <option value="Architettura">Architettura</option>
                        <option value="Chimica">Chimica</option>
                        <option value="Storia">Storia</option>
                        <option value="Altro">Altro</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label style="margin-bottom:5px;">File Modello 3D</label>
                    <div class="upload-anim-box">
                      <div class="folder">
                        <div class="front-side">
                          <div class="tip"></div>
                          <div class="cover"></div>
                        </div>
                        <div class="back-side cover"></div>
                      </div>
                      <label class="custom-file-upload">
                        <input class="title" type="file" name="image" accept=".glb,.gltf" required onchange="updateFileName(this)" />
                        <span id="file-label-text">Scegli file .glb</span>
                      </label>
                    </div>
                </div>

                <input type="submit" class="btn-submit" name="upload" value="Carica Progetto">
            </form>
        </div>
    </div>

    <div id="viewModal" class="modal-overlay" onclick="closeViewModal(event)">
        <div class="modal-view-content" onclick="event.stopPropagation()">
            <span class="close-btn" onclick="closeViewModalForce()">&times;</span>
            <div style="padding: 15px 25px; border-bottom: 1px solid #eee; background:#fbfbfb;">
                <h2 id="modalTitle" style="margin: 0;">Titolo</h2>
                <span id="modalCategory" style="color:#666; font-size:0.9em; background:#eee; padding:2px 8px; border-radius:4px;">Cat</span>
            </div>
            <div style="flex-grow: 1; position: relative; background: #f0f0f0;">
                <model-viewer id="fullViewer" src="" ar ar-modes="webxr scene-viewer quick-look" 
                    camera-controls auto-rotate shadow-intensity="1" style="width: 100%; height: 100%;">
                    <button slot="ar-button" style="background-color: white; border-radius: 30px; border: none; position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); padding: 12px 24px; font-weight:bold; box-shadow: 0 4px 10px rgba(0,0,0,0.2); display:flex; gap:5px; align-items:center;">
                        📱 Vedi in AR
                    </button>
                    <div slot="poster" style="display:flex; justify-content:center; align-items:center; height:100%; color:#666;">
                        Caricamento...
                    </div>
                </model-viewer>
            </div>
        </div>
    </div>

    <script>
        // Update nome file nel bottone custom
        function updateFileName(input) {
            const label = document.getElementById('file-label-text');
            if (input.files && input.files.length > 0) {
                let name = input.files[0].name;
                if(name.length > 20) name = name.substring(0, 17) + "...";
                label.innerText = "📂 " + name;
                document.querySelector('.upload-anim-box').style.background = "linear-gradient(135deg, #43e97b, #38f9d7)";
            } else {
                label.innerText = "Scegli file .glb";
            }
        }

        // Modale Upload
        function openUploadModal() { document.getElementById('uploadModal').style.display = 'flex'; }
        function closeUploadModalForce() { document.getElementById('uploadModal').style.display = 'none'; }
        function closeUploadModal(event) { if (event.target.id === 'uploadModal') closeUploadModalForce(); }

        // Modale View
        function openViewModal(path, title, category) {
            const modal = document.getElementById('viewModal');
            const viewer = document.getElementById('fullViewer');
            viewer.src = path;
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalCategory').innerText = category;
            modal.style.display = 'flex';
        }
        function closeViewModalForce() {
            const modal = document.getElementById('viewModal');
            document.getElementById('fullViewer').src = "";
            modal.style.display = 'none';
        }
        function closeViewModal(event) { if (event.target.id === 'viewModal') closeViewModalForce(); }
    </script>
</body>
</html>