<?php
require 'config.php';
session_start();

# Настройки пагинации
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

# Получаем посты с учетом пагинации
$stmt = $pdo->prepare('SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.is_private = 0 LIMIT ? OFFSET ?');
$stmt->execute([$limit, $offset]);
$posts = $stmt->fetchAll();

# Считаем общее количество постов для пагинации
$count_stmt = $pdo->query('SELECT COUNT(*) FROM posts WHERE posts.is_private = 0');
$total_posts = $count_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

# Получаем все теги для выпадающего списка
$tags_stmt = $pdo->query('SELECT DISTINCT tags FROM posts WHERE tags IS NOT NULL AND tags != ""');
$tags = $tags_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Блог</title>
</head>
<body>
    <?= include('navbar.php'); ?>

    <div class="container">
        <h1>Публичные посты</h1>

        <!-- Сортировка по тегам -->
        <form action="index.php" method="get">
            <label for="tag">Сортировка по тегам:</label>
            <select name="tag" onchange="this.form.submit()">
                <option value="">Все теги</option>
                <?php foreach ($tags as $tag_group): ?>
                    <?php foreach (explode(',', $tag_group['tags']) as $tag): ?>
                        <option value="<?= htmlspecialchars($tag) ?>" <?= $selected_tag == $tag ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tag) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <h2><a href="post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <p><?= htmlspecialchars($post['content']) ?></p>
                    <p><small>Автор: <?= htmlspecialchars($post['username']) ?></small></p>
                    <!-- Вывод тегов -->
                    <?php if (!empty($post['tags'])): ?>
                        <p>Теги: <?= htmlspecialchars($post['tags']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Постов нет.</p>
        <?php endif; ?>

        <!-- Пагинация -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="index.php?page=<?= $i ?>&tag=<?= urlencode($selected_tag) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
