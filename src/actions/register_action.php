<?php
session_start();
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $login = $_POST['login'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, login, password) VALUES (:name, :login, :hashedPassword)");
        $stmt->execute([':name' => $name, ':login' => $login, ':hashedPassword' => $hashedPassword]);

        $_SESSION['success_message'] = 'Conta criada com sucesso!';
        header('Location: ../views/index.php');
        exit;
    } catch (\PDOException $e) {
        $_SESSION['error_message'] = "Erro ao criar a conta, tente novamente";
        header('Location: ../views/register.php');
        exit;
    }
}
?>