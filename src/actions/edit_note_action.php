<?php
session_start();
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note_id = $_POST['note_id'];
    $title = $_POST['edit_title'];
    $content = $_POST['edit_content'];

    try {
        $stmt = $pdo->prepare("UPDATE notes SET title = :title, content = :content, updated_at = NOW() WHERE id = :note_id");
        $stmt->execute([':title' => $title, ':content' => $content, ':note_id' => $note_id]);

        $_SESSION['success_message'] = 'Anotação atualizada com sucesso!';
        header('Location: ../views/dashboard.php');
        exit;
    } catch (\PDOException $e) {
        $_SESSION['error_message'] = "Erro ao atualizar a anotação, tente novamente";
        header('Location: ../views/dashboard.php');
        exit;
    }
}
?>
