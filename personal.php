<?php
session_start();
include 'conn.php';

// Controllo sessione
if (!isset($_SESSION['username'])) {
    header("Location: ./index.php");
    exit();
}

$nome_visualizzato = htmlspecialchars(ucfirst($_SESSION['nome']) . " " . ucfirst($_SESSION['cognome']));
$username_corrente = $_SESSION['username'];

// Controllo connessione DB
if (!$conn) {
    die("Errore di connessione al database.");
}

// 1. PREPARED STATEMENT per sicurezza (Preleviamo i dati dell'utente loggato)
$stmt = $conn->prepare("SELECT * FROM utenti JOIN ruoli ON utenti.CODRuolo = ruoli.IDRuolo WHERE username = ?");
$stmt->bind_param("s", $username_corrente);
$stmt->execute();
$result = $stmt->get_result();
$currentUser = $result->fetch_assoc();
$stmt->close();

$isAdmin = ($currentUser['Descrizione'] === 'Super user');
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Personale</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="https://mdbootstrap.com/api/snippets/static/download/MDB5-Free_6.1.0/css/mdb.min.css">
    
    <link rel="stylesheet" href="../Image-Gallary/style.css">
    <link rel="stylesheet" href="personal.css">
</head>

<body>

    <div class="sidebar-nav">
        <a href="../Image-Gallary/index.php" class="sidebar-btn" title="Home">
            <svg class="icon" viewBox="0 0 1024 1024" fill="currentColor" height="1em" width="1em">
                <path d="M946.5 505L560.1 118.8l-25.9-25.9a31.5 31.5 0 0 0-44.4 0L77.5 505a63.9 63.9 0 0 0-18.8 46c.4 35.2 29.7 63.3 64.9 63.3h42.5V940h691.8V614.3h43.4c17.1 0 33.2-6.7 45.3-18.8a63.6 63.6 0 0 0 18.7-45.3c0-17-6.7-33.1-18.8-45.2zM568 868H456V664h112v204zm217.9-325.7V868H632V640c0-22.1-17.9-40-40-40H432c-22.1 0-40 17.9-40 40v228H238.1V542.3h-96l370-369.7 23.1 23.1L882 542.3h-96.1z"></path>
            </svg>
        </a>

        <a href="./personal.php" class="sidebar-btn" title="Area Personale">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor" height="1em" width="1em">
                <path d="M12 2.5a5.5 5.5 0 0 1 3.096 10.047 9.005 9.005 0 0 1 5.9 8.181.75.75 0 1 1-1.499.044 7.5 7.5 0 0 0-14.993 0 .75.75 0 0 1-1.5-.045 9.005 9.005 0 0 1 5.9-8.18A5.5 5.5 0 0 1 12 2.5ZM8 8a4 4 0 1 0 8 0 4 4 0 0 0-8 0Z"></path>
            </svg>
        </a>

        <a href="./logout.php" class="sidebar-btn" title="Logout">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" height="1em" width="1em">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </a>
    </div>

    <div class="container" style="margin-top: 80px; padding-bottom: 50px;">
        
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card card-profile shadow-4-strong">
                    <div class="card-body text-center">
                        <div class="avatar-placeholder">
                            <?= strtoupper(substr($currentUser['Nome'], 0, 1)) ?>
                        </div>
                        <h4 class="mb-2"><?= htmlspecialchars(ucfirst($currentUser['Nome']) . " " . ucfirst($currentUser['Cognome'])) ?></h4>
                        <p class="text-muted mb-4 badge bg-primary"><?= htmlspecialchars($currentUser['Descrizione']) ?></p>
                        
                        <div class="list-group list-group-flush text-start">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-user me-2 text-secondary"></i>Username</span>
                                <span class="fw-bold"><?= htmlspecialchars($currentUser['Username']) ?></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-envelope me-2 text-secondary"></i>Email</span>
                                <span><?= htmlspecialchars($currentUser['Email'] ?? 'Non specificata') ?></span>
                            </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($isAdmin): ?>
            <?php
            // Query Admin: Preleva tutti tranne se stesso
            $stmtList = $conn->prepare("SELECT * FROM utenti JOIN ruoli ON utenti.CODRuolo = ruoli.IDRuolo WHERE Username != ? ORDER BY Username");
            $stmtList->bind_param("s", $username_corrente);
            $stmtList->execute();
            $resultList = $stmtList->get_result();
            
            // Conteggio utenti semplici
            $sqlCount = "SELECT count(*) as Numero FROM utenti WHERE CODRuolo = 2";
            $resCount = mysqli_query($conn, $sqlCount);
            $countUsers = mysqli_fetch_assoc($resCount)['Numero'];
            ?>

            <div class="card mt-5 shadow-2-strong">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="fas fa-users-cog me-2"></i>Gestione Utenti</h5>
                    <span class="badge bg-secondary">Totale Utenti Semplici: <?= $countUsers ?></span>
                </div>
                <div class="card-body">
                    <form action='./modify.php' method='POST' id="adminForm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-admin">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Username</th>
                                        <th>Nome</th>
                                        <th>Cognome</th>
                                        <th>Email</th>
                                        <th>Ruolo</th>
                                        <th class="text-center">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $resultList->fetch_assoc()): 
                                        $u = htmlspecialchars($row['Username']); // Sanitizzazione per output HTML
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-muted">
                                            <?= $u ?>
                                            <input type="hidden" name="Username-<?= $u ?>" value="<?= $u ?>">
                                        </td>
                                        
                                        <td>
                                            <input type="text" name="Nome-<?= $u ?>" value="<?= htmlspecialchars($row['Nome']) ?>" class="form-control-plaintext">
                                        </td>
                                        <td>
                                            <input type="text" name="Cognome-<?= $u ?>" value="<?= htmlspecialchars($row['Cognome']) ?>" class="form-control-plaintext">
                                        </td>
                                        <td>
                                            <input type="email" name="Email-<?= $u ?>" value="<?= htmlspecialchars($row['Email']) ?>" class="form-control-plaintext">
                                        </td>
                                        
                                        <td>
                                            <select name="Descrizione-<?= $u ?>" class="form-select form-select-sm border-0 bg-transparent">
                                                <option value="2" <?= $row['CODRuolo'] == 2 ? 'selected' : '' ?>>Simple user</option>
                                                <option value="1" <?= $row['CODRuolo'] == 1 ? 'selected' : '' ?>>Super user</option>
                                            </select>
                                        </td>

                                        <td class="text-center">
                                            <button type="submit" name="delete" value="delete-<?= $u ?>" class="btn btn-link text-danger btn-sm p-0" onclick="return confirm('Sei sicuro di voler eliminare <?= $u ?>?');">
                                                <i class="fas fa-trash-alt fa-lg"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <button type="submit" class="btn btn-primary btn-lg" name="update">
                                <i class="fas fa-save me-2"></i>Salva Modifiche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php 
                $stmtList->close();
            ?>
        <?php endif; ?>

    </div>

    <script type="text/javascript" src="https://mdbootstrap.com/api/snippets/static/download/MDB5-Free_6.1.0/js/mdb.min.js"></script>
</body>
</html>