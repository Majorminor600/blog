<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

# Проверяем, уже есть ли подписка
$stmt = $pdo->prepare('SELECT * FROM subscriptions WHERE subscriber_id = ? AND subscribed_to_id = ?');
$stmt->execute([$user_id, $post_id]);
$subscription = $stmt->fetch();

if (!$subscription) {
    # Добавляем новую подписку
    $stmt = $pdo->prepare('INSERT INTO subscriptions (subscriber_id, subscribed_to_id) VALUES (?, ?)');
    $stmt->execute([$user_id, $post_id]);
}

header('Location: post.php?id=' . $post_id);
exit();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Подписка</title>
</head>
<body>
    <div class="container">
        <p>Подписка оформлена!</p>
        <a href="index.php">Вернуться на главную</a>
    </div>
</body>
</html>
