<?php
session_start();

// Inclusione DB
include '../login/conn.php';           
include '../Image-Gallary/connect.php'; 

// 1. SICUREZZA
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header("Location: ../login/index.php");
    exit();
}

// 2. LOGICA DELETE UTENTE
if (isset($_GET['delete_user'])) {
    $userToDelete = mysqli_real_escape_string($conn, $_GET['delete_user']);
    if ($userToDelete != $_SESSION['username']) {
        mysqli_query($conn, "DELETE FROM utenti WHERE Username = '$userToDelete'");
    }
    header("Location: admin.php");
    exit();
}

// 3. LOGICA DELETE PROGETTO (Basata su PATH)
if (isset($_GET['delete_project'])) {
    // Recuperiamo il path dalla URL
    $pathToDelete = urldecode($_GET['delete_project']);
    $safePath = mysqli_real_escape_string($connect, $pathToDelete);
    
    // 1. Cancella file fisico
    $filePath = "../Image-Gallary/" . $pathToDelete;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // 2. Cancella commenti
    mysqli_query($connect, "DELETE FROM comments WHERE project_ref = '$safePath'");

    // 3. Cancella record DB (usando path come chiave)
    mysqli_query($connect, "DELETE FROM gallary WHERE path = '$safePath'");
    
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://mdbootstrap.com/api/snippets/static/download/MDB5-Free_6.1.0/css/mdb.min.css">
    
    <link rel="stylesheet" href="../Image-Gallary/style.css">
    <link rel="stylesheet" href="personal.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <div class="sidebar-nav">
        <a href="../Index.html" class="sidebar-btn" title="Home"><i class="fas fa-home icon"></i></a>
        <a href="../Image-Gallary/index.php" class="sidebar-btn" title="Upload"><i class="fas fa-vr-cardboard"></i></a>
        <a href="#" class="sidebar-btn" title="Profilo"><i class="fas fa-user icon"></i></a>

        <button class="sidebar-btn" title="Cambia Tema" id="theme-toggle">
            <i class="fas fa-moon icon"></i>
        </button>
    </div>

    <div class="admin-container">
        <h1 style="text-align:center; margin-bottom: 10px;">System Admin Console</h1>

        <div class="panel-section">
            <h2><i class="fa-solid fa-users-gear"></i> Gestione Utenti</h2>
            <table>
                <thead>
                    <tr>
                        <th>USERNAME</th>
                        <th>NOME COMPLETO</th>
                        <th>EMAIL</th>
                        <th>RUOLO</th>
                        <th>AZIONI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $resultUsers = mysqli_query($conn, "SELECT * FROM utenti");
                    if($resultUsers):
                        while($u = mysqli_fetch_assoc($resultUsers)):
                    ?>
                    <tr>
                        <td style="font-weight: bold; color: var(--text-main);"><?php echo htmlspecialchars($u['Username']); ?></td>
                        <td><?php echo htmlspecialchars($u['Nome'] . " " . $u['Cognome']); ?></td>
                        <td><?php echo htmlspecialchars($u['Email']); ?></td>
                        <td>
                            <?php if($u['Admin'] == 1): ?>
                                <span class="badge admin">ADMIN</span>
                            <?php else: ?>
                                <span class="badge user">USER</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($u['Username'] === $_SESSION['username']): ?>
                                <span style="color: var(--text-sec); font-size: 0.8rem; font-style: italic;">(Sessione Attiva)</span>
                            <?php else: ?>
                                <a href="admin.php?delete_user=<?php echo urlencode($u['Username']); ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('ATTENZIONE: Eliminare l\'utente <?php echo $u['Username']; ?>?');">
                                    <i class="fa-solid fa-trash"></i> PURGE
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>

        <div class="panel-section">
            <h2><i class="fa-solid fa-database"></i> Repository Progetti</h2>
            <table>
                <thead>
                    <tr>
                        <th>N.</th> <th>TITOLO</th>
                        <th>CATEGORIA</th>
                        <th>FILE PATH</th>
                        <th>AZIONI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $resultProjs = mysqli_query($connect, "SELECT * FROM gallary"); 
                    $counter = 1; // Contatore visivo
                    if($resultProjs):
                        while($p = mysqli_fetch_assoc($resultProjs)):
                    ?>
                    <tr>
                        <td style="color: var(--text-sec);"><?php echo $counter++; ?></td>
                        <td style="color: var(--primary); font-weight: bold;"><?php echo htmlspecialchars($p['title']); ?></td>
                        <td><?php echo htmlspecialchars($p['category']); ?></td>
                        <td style="font-family: monospace; color: var(--text-sec); font-size: 0.85rem;">
                            <?php echo htmlspecialchars($p['path']); ?>
                        </td>
                        <td>
                            <a href="admin.php?delete_project=<?php echo urlencode($p['path']); ?>" 
                               class="btn-delete" 
                               onclick="return confirm('ATTENZIONE: Eliminare il progetto?');">
                                <i class="fa-solid fa-trash"></i> DELETE
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
    <script src="./admin.js"></script>
</body>
</html>
