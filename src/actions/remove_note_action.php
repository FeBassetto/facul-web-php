<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php?error=Você precisa estar logado para acessar esta página.');
    exit;
}

require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_id = $_POST['note_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM notes WHERE id = :note_id");
        $stmt->execute(['note_id' => $note_id]);

        header('Location: ../views/dashboard.php');
        exit;
    } catch (\PDOException $e) {
        $_SESSION['error_message'] = 'Erro ao remover a anotação. Tente novamente mais tarde.';
        header('Location: ../views/dashboard.php');
        exit;
    }
} else {
    header('Location: ../views/dashboard.php');
    exit;
}
?>