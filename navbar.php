<div class="navbar">
    <a href="index.php">Главная</a>
    <a href="add_post.php">Добавить пост</a>
    <a href="subscriptions.php">Подписки</a>
    <a href="profile.php">Профиль</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <span style="color: #ffffff; margin-left: 15px;">Привет, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php">Выйти</a>
    <?php else: ?>
        <a href="register.php">Регистрация</a>
        <a href="login.php">Вход</a>
    <?php endif; ?>
</div>
