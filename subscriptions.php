<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM subscriptions WHERE subscriber_id = ?');
$stmt->execute([$user_id]);
$subscriptions = $stmt->fetchAll();

$subscribed_posts = [];
if (count($subscriptions) > 0) {
    $placeholders = implode(',', array_fill(0, count($subscriptions), '?'));
    $subscribed_to_ids = array_column($subscriptions, 'subscribed_to_id');

    $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.user_id IN ($placeholders)");
    $stmt->execute($subscribed_to_ids);
    $subscribed_posts = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Подписки</title>
</head>
<body>
    <?= include('navbar.php'); ?>

    <div class="container">
        <h1>Посты пользователей, на которых вы подписаны</h1>
        <?php if (count($subscribed_posts) > 0): ?>
            <?php foreach ($subscribed_posts as $post): ?>
                <div class="post">
                    <h2><a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <p><?= htmlspecialchars($post['content']) ?></p>
                    <p><small>Автор: <?= htmlspecialchars($post['username']) ?></small></p>

                    <form action="unsubscribe.php" method="post">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit">Отписаться от этого автора</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Нет постов от пользователей, на которых вы подписаны.</p>
        <?php endif; ?>
    </div>
</body>
</html>
