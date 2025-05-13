<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<section class="hero">
    <div class="hero-content">
        <h2>Добро пожаловать в SLAVA TECH</h2>
        <p>Лучшие игровые устройства для настоящих геймеров</p>
        <a href="products.php" class="btn">Посмотреть устройства</a>
    </div>
</section>

<section class="features">
    <div class="feature">
        <i class="fas fa-truck"></i>
        <h3>Быстрая доставка</h3>
        <p>Доставка по всей России за 1-3 дня</p>
    </div>
    <div class="feature">
        <i class="fas fa-shield-alt"></i>
        <h3>Гарантия качества</h3>
        <p>Гарантия на все товары 1 год</p>
    </div>
    <div class="feature">
        <i class="fas fa-headset"></i>
        <h3>Поддержка 24/7</h3>
        <p>Наша поддержка всегда готова помочь</p>
    </div>
</section>

<section class="popular-products">
    <h2>Популярные устройства</h2>
    <div class="products-grid">
        <?php
        $stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="product-card">';
            echo '<img src="assets/images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
            echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
            echo '<p>' . number_format($row['price'], 2) . ' ₽</p>';
            echo '<a href="products.php?product_id=' . $row['id'] . '" class="btn">Подробнее</a>';
            echo '</div>';
        }
        ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>