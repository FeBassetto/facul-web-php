<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header('Location: index.php?error=Você precisa estar logado para acessar esta página.');
    exit;
}

require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, content) VALUES (:user_id, :title, :content)");
        $stmt->execute(['user_id' => $user_id, 'title' => $title, 'content' => $content]);

        header('Location: ../views/dashboard.php');
        exit;
    } catch (\PDOException $e) {
        $_SESSION['error_message'] = 'Erro ao adicionar a anotação. Tente novamente mais tarde.';
        header('Location: ../views/dashboard.php');
        exit;
    }
} else {
    header('Location: ../views/dashboard.php');
    exit;
}
?>