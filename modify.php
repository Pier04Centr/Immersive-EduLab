<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ./index.php");
} else {
    include 'conn.php';

    if (!$conn) {
        die("<script> alert('Connessione non riusita') </script>");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['delete'])) {
            $username = substr($_POST['delete'], strlen("delete-"));
            $sql = 'DELETE FROM utenti WHERE Username = "' . $username . '"';
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                die("<script> alert('Errore durante l\'eliminazione dell\'utente ".$username."') </script>");
            } else {
                echo "<script> alert('Utente eliminato con successo') </script>";
                header("Refresh:0; url=./personal.php");
            }

        } else {
            foreach ($_POST as $key => $value) {
                if (strpos($key, "Username-") !== false) {
                    $username = substr($key, strlen("Username-"));
                    $nome = "";
                    $cognome = "";
                    $descrizione = "";
        
                    $nome_post = "Nome-" . $username;
                    $cognome_post = "Cognome-" . $username;
                    $descrizione_post = "Descrizione-" . $username;
                    $nome = strtolower($_POST[$nome_post]);
                    $cognome = strtolower($_POST[$cognome_post]);
                    $descrizione = intval($_POST[$descrizione_post]);
        
                    $sql = 'UPDATE utenti SET Nome = "' . $nome . '", Cognome = "' . $cognome . '", CODRuolo = ' . $descrizione . ' WHERE Username = "' . $username . '"';
                    $result = mysqli_query($conn, $sql);
                    if (!$result) {
                        die("<script> alert('Errore durante l\'aggiornamento dei dati per l\'utente ".$username."') </script>");
                    }
                }
            }
    
            echo "<script> alert('Dati aggiornati con successo') </script>";
            header("Refresh:0; url=./personal.php");
        }
    } else {
        header("Location: ./index.php");
    }
}
