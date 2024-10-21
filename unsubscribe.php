<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];

# Удаляем подписку
$stmt = $pdo->prepare('DELETE FROM subscriptions WHERE subscriber_id = ? AND subscribed_to_id = ?');
$stmt->execute([$user_id, $post_id]);

header('Location: post.php?id=' . $post_id);
exit();
?>
