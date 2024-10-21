<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $is_private = isset($_POST['is_private']) ? 1 : 0;
    $tags = $_POST['tags'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO posts (user_id, title, content, is_private, tags) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$user_id, $title, $content, $is_private, $tags]);

    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Добавить пост</title>
</head>
<body>
    <?= include('navbar.php'); ?>

    <div class="container">
        <form action="add_post.php" method="post">
            <label for="title">Заголовок:</label>
            <input type="text" name="title" required>
            
            <label for="content">Содержание:</label>
            <textarea name="content" rows="5" required></textarea>

            <label for="tags">Теги (через запятую):</label>
            <input type="text" name="tags">
            
            <label for="is_private">Приватный пост:</label>
            <input type="checkbox" name="is_private">
            
            <button type="submit">Добавить пост</button>
        </form>
    </div>
</body>
</html>