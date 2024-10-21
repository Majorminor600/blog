<?php
require 'config.php';
session_start();

$post_id = $_GET['id'];

# Получаем данные поста
$stmt = $pdo->prepare('SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?');
$stmt->execute([$post_id]);
$post = $stmt->fetch();

# Получаем комментарии к посту
$comments_stmt = $pdo->prepare('SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ?');
$comments_stmt->execute([$post_id]);
$comments = $comments_stmt->fetchAll();

# Проверяем подписку пользователя на пост
$is_subscribed = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $subscription_stmt = $pdo->prepare('SELECT * FROM subscriptions WHERE subscriber_id = ? AND subscribed_to_id = ?');
    $subscription_stmt->execute([$user_id, $post['user_id']]);
    $is_subscribed = $subscription_stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title><?= htmlspecialchars($post['title']) ?></title>
</head>
<body>
    <?= include('navbar.php'); ?>

    <div class="container">
        <h1><?= htmlspecialchars($post['title']) ?></h1>
        <p><?= htmlspecialchars($post['content']) ?></p>
        <p><small>Автор: <?= htmlspecialchars($post['username']) ?></small></p>

        <!-- Проверяем, подписан ли пользователь на автора, и показываем соответствующую кнопку -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($is_subscribed): ?>
                <form action="unsubscribe.php" method="post">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <button type="submit">Отписаться от постов этого автора</button>
                </form>
            <?php else: ?>
                <form action="subscribe.php" method="post">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <button type="submit">Подписаться на посты этого автора</button>
                </form>
            <?php endif; ?>

            <!-- Ссылки на редактирование и удаление поста, если пользователь является его автором -->
            <?php if ($_SESSION['user_id'] == $post['user_id']): ?>
                <a href="edit_post.php?id=<?= $post_id ?>">Редактировать пост</a>
                <form action="delete_post.php" method="post" style="display:inline;">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <button type="submit" onclick="return confirm('Вы уверены, что хотите удалить пост?')">Удалить пост</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>

        <h2>Комментарии:</h2>
        <?php if (count($comments) > 0): ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <p><?= htmlspecialchars($comment['comment']) ?></p>
                    <p><small>Автор комментария: <?= htmlspecialchars($comment['username']) ?></small></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Комментариев пока нет.</p>
        <?php endif; ?>

        <form action="add_comment.php?post_id=<?= $post_id ?>" method="post">
            <label for="comment">Оставить комментарий:</label>
            <textarea name="comment" rows="4" required></textarea>
            <button type="submit">Добавить комментарий</button>
        </form>
    </div>
</body>
</html>
