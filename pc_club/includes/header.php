<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC Club - Лучшие игровые устройства</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php">SLAVA TECH</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <li><a href="products.php">Устройства</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="account.php">Мой аккаунт</a></li>
                        <li><a href="cart.php">Корзина</a></li>
                        <li><a href="logout.php">Выйти</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="./admin/">Админ-панель</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="login.php">Войти</a></li>
                        <li><a href="register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container"></main>