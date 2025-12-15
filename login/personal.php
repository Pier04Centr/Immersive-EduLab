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

        <button onclick="toggleDarkMode()" class="sidebar-btn" title="Cambia Tema" style="margin-bottom:10px;">
            <i id="theme-icon" class="fas fa-moon icon"></i>
        </button>
    </div>

    <div class="container" style="padding-bottom: 50px;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card card-profile">
                    <div class="card-body text-center p-5">
                        
                        <div class="avatar-placeholder">
                            <?= strtoupper(substr($currentUser['Nome'], 0, 1)) ?>
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

        // Al caricamento, controlla se l'utente aveva già attivato il tema scuro
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
            document.getElementById('theme-icon').className = 'fas fa-sun icon';
        }
    </script>
</body>
</html>