<?php require_once 'includes/config.php'; ?>
<?php
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}
?>
<?php require_once 'includes/header.php'; ?>

<section class="cart">
    <h2>Ваша корзина</h2>
    
    <?php
    if (isset($_SESSION['cart_success'])) {
        echo '<div class="alert success">' . $_SESSION['cart_success'] . '</div>';
        unset($_SESSION['cart_success']);
    }
    if (isset($_SESSION['cart_error'])) {
        echo '<div class="alert error">' . $_SESSION['cart_error'] . '</div>';
        unset($_SESSION['cart_error']);
    }
    ?>
    
    <div class="cart-items">
        <?php
        $total = 0;
        $stmt = $pdo->prepare("
            SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity, p.stock 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;
                
                echo '<div class="cart-item">';
                echo '<img src="assets/images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                echo '<div class="item-details">';
                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                echo '<p class="price">' . number_format($row['price'], 2) . ' ₽</p>';
                echo '<form action="update_cart.php" method="post">';
                echo '<input type="hidden" name="cart_id" value="' . $row['cart_id'] . '">';
                echo '<input type="number" name="quantity" value="' . $row['quantity'] . '" min="1" max="' . $row['stock'] . '">';
                echo '<button type="submit" class="btn">Обновить</button>';
                echo '</form>';
                echo '<form action="remove_from_cart.php" method="post">';
                echo '<input type="hidden" name="cart_id" value="' . $row['cart_id'] . '">';
                echo '<button type="submit" class="btn danger">Удалить</button>';
                echo '</form>';
                echo '</div>';
                echo '<div class="item-subtotal">';
                echo '<p>' . number_format($subtotal, 2) . ' ₽</p>';
                echo '</div>';
                echo '</div>';
            }
            
            echo '<div class="cart-total">';
            echo '<h3>Итого: ' . number_format($total, 2) . ' ₽</h3>';
            echo '<a href="checkout.php" class="btn">Оформить заказ</a>';
            echo '</div>';
        } else {
            echo '<p>Ваша корзина пуста.</p>';
            echo '<a href="products.php" class="btn">Перейти к покупкам</a>';
        }
        ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>