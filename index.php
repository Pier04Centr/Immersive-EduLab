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
    
    // GESTIONE MESSAGGI DALLA SESSIONE (PRG Pattern)
    $msg = "";
    if (isset($_SESSION['upload_msg'])) {
        $msg = $_SESSION['upload_msg']; // Recupera il messaggio
        unset($_SESSION['upload_msg']); // Lo cancella per non mostrarlo al prossimo refresh
    }

    // LOGICA UPLOAD FILE
    if(isset($_POST["upload"])){
        // Sanificazione input
        $title = mysqli_real_escape_string($conn, $_POST["title"]);
        $category = mysqli_real_escape_string($conn, $_POST["category"]);
        
        $filename = $_FILES['image']['name'];
        $tempname = $_FILES['image']['tmp_name'];
        
        // Estrazione estensione
        $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = array('glb', 'gltf'); 

        if(in_array($fileExt, $allowed)){
            $newFileName = uniqid("model_", true) . "." . $fileExt;
            $folder = "./uploads/" . $newFileName; 

            if (!file_exists('./uploads')) {
                mkdir('./uploads', 0777, true);
            }

            if(move_uploaded_file($tempname, $folder)){
                $query = "INSERT INTO gallary (title, category, path) VALUES ('$title', '$category', '$newFileName')";
                if(mysqli_query($conn, $query)){
                    // SUCCESSO: Salviamo il messaggio in sessione e facciamo REDIRECT
                    $_SESSION['upload_msg'] = "<div class='alert success'>✅ Modello 3D caricato con successo!</div>";
                    header("Location: index.php"); // <--- IL TRUCCO È QUI
                    exit();
                } else {
                    $msg = "<div class='alert error'>❌ Errore Database: " . mysqli_error($conn) . "</div>";
                }
            } else {
                $msg = "<div class='alert error'>❌ Errore nello spostare il file.</div>";
            }
        } else {
            $msg = "<div class='alert error'>⚠️ Formato non valido! Carica solo file .glb</div>";
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
    
    <style>
        /* --- CSS STYLES --- */
        :root {
            --primary: #4a90e2;
            --bg: #f4f7f6;
            --card-bg: #ffffff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg);
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Navbar */
        header {
            background-color: #333;
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Sezione Upload */
        .upload-section {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        .form-group { margin-bottom: 15px; }
        input[type="text"], select, input[type="file"] { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            box-sizing: border-box; 
        }
        
        .btn {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn:hover { background-color: #357abd; }

        /* Filtri Categoria */
        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            justify-content: center;
        }

        .filter-btn {
            text-decoration: none;
            color: #333;
            background: #e0e0e0;
            padding: 8px 16px;
            border-radius: 20px;
            transition: 0.3s;
            font-size: 14px;
        }

        .filter-btn:hover, .filter-btn.active {
            background-color: var(--primary);
            color: white;
        }

        /* GRIGLIA RESPONSIVE */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .card {
            background: var(--card-bg);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 350px;
            display: flex;
            flex-direction: column;
            cursor: pointer; /* Indica che è cliccabile */
        }

        .card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }

        .card model-viewer {
            width: 100%;
            height: 250px;
            background-color: #f9f9f9;
        }

        .card-info {
            padding: 15px;
            text-align: center;
            flex-grow: 1;
            border-top: 1px solid #eee;
        }

        /* --- STILI MODALE (POPUP) --- */
        .modal-overlay {
            display: none; /* Nascosto di default */
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.85);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            width: 90%;
            max-width: 1000px;
            height: 80vh;
            background: white;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            color: #333;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            z-index: 10;
            line-height: 1;
        }
        .close-btn:hover { color: #e02424; }

        /* Messaggi Alert */
        .alert { padding: 12px; margin-bottom: 20px; border-radius: 6px; border: 1px solid transparent; }
        .success { background-color: #d1e7dd; color: #0f5132; border-color: #badbcc; }
        .error { background-color: #f8d7da; color: #842029; border-color: #f5c2c7; }

    </style>
</head>
<body>

    <header>
        <div style="font-size: 1.5rem; font-weight: bold;">OpenAR Campus 🎓</div>
        <span>Ciao, <?php echo $nome_utente; ?></span>
    </header>

    <div class="container">
        
        <section class="upload-section">
            <h2 style="margin-top:0;">Carica un nuovo modello 3D</h2>
            <?php echo $msg; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="title" placeholder="Titolo del modello (es. Cuore Umano)" required>
                </div>
                <div class="form-group">
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
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">File Modello (.glb)</label>
                    <input type="file" name="image" accept=".glb,.gltf" required>
                </div>
                <input type="submit" class="btn" name="upload" value="Carica Progetto">
            </form>
        </section>

        <section>
            <h1 style="text-align:center; margin-bottom: 20px;">Esplora Progetti</h1>
            
            <div class="filters">
                <a href="index.php" class="filter-btn">Tutti</a>
                <a href="?category=Ingegneria" class="filter-btn">Ingegneria</a>
                <a href="?category=Medicina" class="filter-btn">Medicina</a>
                <a href="?category=Architettura" class="filter-btn">Architettura</a>
                <a href="?category=Chimica" class="filter-btn">Chimica</a>
            </div>

            <div class="gallery-grid">
                <?php 
                    // Logica Filtro
                    $whereClause = "";
                    if(isset($_GET['category']) && !empty($_GET['category'])) {
                        $cat = mysqli_real_escape_string($conn, $_GET['category']);
                        $whereClause = "WHERE category='$cat'";
                    }

                    // Query al DB
                    // NOTA: Se hai ricreato la tabella e non hai 'id', rimuovi 'ORDER BY id DESC'
                    $query = "SELECT * FROM gallary $whereClause ORDER BY title DESC";
                    $data = mysqli_query($conn, $query);

                    if (mysqli_num_rows($data) > 0) {
                        while ($res = mysqli_fetch_assoc($data)) {
                            $fullPath = "./uploads/" . $res['path'];
                            
                            // Prepariamo stringhe sicure per JS (gestione apostrofi nei titoli)
                            $jsTitle = htmlspecialchars($res['title'], ENT_QUOTES);
                            $jsCat = htmlspecialchars($res['category'], ENT_QUOTES);
                ?>
                            <div class='card' onclick="openModal('<?php echo $fullPath; ?>', '<?php echo $jsTitle; ?>', '<?php echo $jsCat; ?>')">
                                
                                <model-viewer 
                                    src="<?php echo $fullPath; ?>" 
                                    auto-rotate 
                                    rotation-per-second="30deg"
                                    interaction-prompt="none"
                                    disable-zoom
                                    shadow-intensity="1"
                                    style="pointer-events: none;">
                                </model-viewer>
                                
                                <div class='card-info'>
                                    <div style="font-weight:bold; font-size:1.1em;"><?php echo $res['title']; ?></div>
                                    <div style="color: #666; font-size:0.9em; margin-top:5px;"><?php echo $res['category']; ?></div>
                                </div>
                            </div>
                <?php 
                        }
                    } else {
                        echo "<p style='text-align:center; grid-column: 1/-1; padding: 20px;'>Nessun progetto trovato.</p>";
                    }
                ?>
            </div>
        </section>
    </div>

    <div id="projectModal" class="modal-overlay" onclick="closeModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            
            <span class="close-btn" onclick="closeModalForce()">&times;</span>
            
            <div style="padding: 15px 25px; border-bottom: 1px solid #eee; background: #fcfcfc;">
                <h2 id="modalTitle" style="margin: 0; font-size: 1.5em;">Titolo</h2>
                <span id="modalCategory" style="color: #666; font-size: 0.9em; background: #eee; padding: 2px 8px; border-radius: 4px;">Cat</span>
            </div>

            <div style="flex-grow: 1; position: relative; background: #f0f0f0;">
                <model-viewer 
                    id="fullViewer" 
                    src="" 
                    ar 
                    ar-modes="webxr scene-viewer quick-look" 
                    camera-controls 
                    auto-rotate 
                    shadow-intensity="1"
                    style="width: 100%; height: 100%;">
                    
                    <button slot="ar-button" style="
                        background-color: white; 
                        border-radius: 30px; 
                        border: none; 
                        position: absolute; 
                        bottom: 20px; 
                        left: 50%; 
                        transform: translateX(-50%);
                        padding: 12px 24px; 
                        font-weight:bold; 
                        font-size: 16px;
                        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
                        cursor: pointer;
                        display: flex; align-items: center; gap: 8px;">
                        📱 Vedi in AR
                    </button>
                    
                    <div slot="poster" style="display:flex; justify-content:center; align-items:center; height:100%; color:#666;">
                        Caricamento modello 3D...
                    </div>
                </model-viewer>
            </div>
        </div>
    </div>

    <script>
        function openModal(path, title, category) {
            const modal = document.getElementById('projectModal');
            const viewer = document.getElementById('fullViewer');
            
            // Imposta i dati
            viewer.src = path;
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalCategory').innerText = category;
            
            // Mostra modale
            modal.style.display = 'flex';
        }

        function closeModal(event) {
            // Chiude solo se clicchi sullo sfondo scuro
            if (event.target.id === 'projectModal') {
                closeModalForce();
            }
        }

        function closeModalForce() {
            const modal = document.getElementById('projectModal');
            const viewer = document.getElementById('fullViewer');
            
            modal.style.display = 'none';
            // Pulisce la source per fermare il rendering e risparmiare memoria
            setTimeout(() => { viewer.src = ""; }, 200);
        }
    </script>

</body>
</html>