<?php
require 'config.php';
session_start();

# Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = $_GET['id'];

# Получаем данные поста
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? AND user_id = ?');
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    echo 'Пост не найден или у вас нет прав для его редактирования.';
    exit();
}

# Обновляем пост
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $tags = $_POST['tags'];
    $is_private = isset($_POST['is_private']) ? 1 : 0;

    $stmt = $pdo->prepare('UPDATE posts SET title = ?, content = ?, tags = ?, is_private = ? WHERE id = ? AND user_id = ?');
    $stmt->execute([$title, $content, $tags, $is_private, $post_id, $_SESSION['user_id']]);

    header('Location: post.php?id=' . $post_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Редактировать пост</title>
</head>
<body>
    <?= include('navbar.php'); ?>

    <div class="container">
        <h1>Редактировать пост</h1>
        <form action="edit_post.php?id=<?= $post_id ?>" method="post">
            <label for="title">Заголовок:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            
            <label for="content">Содержание:</label>
            <textarea name="content" rows="5" required><?= htmlspecialchars($post['content']) ?></textarea>

            <label for="tags">Теги (через запятую):</label>
            <input type="text" name="tags" value="<?= htmlspecialchars($post['tags']) ?>">

            <label for="is_private">Приватный пост:</label>
            <input type="checkbox" name="is_private" <?= $post['is_private'] ? 'checked' : '' ?>>

            <button type="submit">Сохранить изменения</button>
        </form>
    </div>
</body>
</html>
