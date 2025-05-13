<?php require_once 'includes/config.php'; ?>

<?php
if (isLoggedIn()) {
    header("Location: account.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header("Location: account.php");
            exit();
        } else {
            $error = 'Неверное имя пользователя или пароль';
        }
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<section class="auth-form">
    <h2>Вход</h2>
    <?php if ($error): ?>
        <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Имя пользователя или Email:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Войти</button>
    </form>
    <p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
</section>

<?php require_once 'includes/footer.php'; ?>