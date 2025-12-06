<?php
    session_start();
    
    // 1. Pulisce tutte le variabili di sessione (nome, ruolo, ecc.)
    session_unset();

    // 2. Distrugge fisicamente la sessione sul server
    session_destroy();

    // 3. Reindirizza alla pagina di Login (index.php dentro la cartella login)
    // Nota: Non reindirizzare a 'personal.php' perché senza sessione ti darebbe errore.
    header("Location: index.php"); 
    exit();
?>