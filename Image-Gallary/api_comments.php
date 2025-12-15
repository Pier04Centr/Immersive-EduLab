<?php
include("./connect.php");
session_start();
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// 1. CARICAMENTO COMMENTI
if ($action === 'load') {
    $path = mysqli_real_escape_string($connect, $_POST['path']);
    $query = "SELECT * FROM comments WHERE project_ref = '$path' ORDER BY created_at DESC";
    $result = mysqli_query($connect, $query);
    
    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    echo json_encode($comments);
    exit;
}

// 2. SALVATAGGIO NUOVO COMMENTO
if ($action === 'save') {
    if (!isset($_SESSION['username'])) {
        echo json_encode(['status' => 'error', 'message' => 'Devi essere loggato']);
        exit;
    }

    $path = mysqli_real_escape_string($connect, $_POST['path']);
    $text = mysqli_real_escape_string($connect, $_POST['comment']);
    $user = $_SESSION['username'];

    if (trim($text) === '') exit;

    $query = "INSERT INTO comments (project_ref, username, comment) VALUES ('$path', '$user', '$text')";
    if (mysqli_query($connect, $query)) {
        echo json_encode(['status' => 'success', 'user' => $user, 'date' => date('Y-m-d H:i:s')]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}
?>