<?php
session_start();
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['logged_in'] = true;

            header('Location: ../views/dashboard.php');
            exit;
        } else {
            $_SESSION['error_message'] = 'Login ou senha incorretos.';
            header('Location: ../views/index.php');
            exit;
        }
    } catch (\PDOException $e) {
        $_SESSION['error_message'] = "Erro ao tentar logar: " . $e->getMessage();
        header('Location: ../views/index.php');
        exit;
    }
}
?>