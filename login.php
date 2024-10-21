<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit();
    } else {
        echo "Неверные учетные данные. Попробуйте снова.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Вход</title>
</head>
<body>
    <?= include('navbar.php'); ?>

    <div class="container">
        <form action="login.php" method="post">
            <label for="username">Имя пользователя:</label>
            <input type="text" name="username" required>
            
            <label for="password">Пароль:</label>
            <input type="password" name="password" required>
            
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>