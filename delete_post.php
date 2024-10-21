<?php
require 'config.php';
session_start();

# Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = $_POST['post_id'];

# Удаляем пост, если пользователь является его автором
$stmt = $pdo->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
$stmt->execute([$post_id, $_SESSION['user_id']]);

header('Location: index.php');
exit();
?>
