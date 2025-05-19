<?php require_once 'includes/config.php'; ?>
<?php
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_count = $stmt->fetchColumn();

if ($cart_count == 0) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount) 
            SELECT ?, SUM(p.price * c.quantity) 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
        $order_id = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            SELECT ?, c.product_id, c.quantity, p.price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$order_id, $_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("
            UPDATE products p
            JOIN cart c ON p.id = c.product_id
            SET p.stock = p.stock - c.quantity
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        $pdo->commit();
        
        $_SESSION['order_success'] = "Заказ #$order_id успешно оформлен!";
        header("Location: account.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['checkout_error'] = "Ошибка при оформлении заказа: " . $e->getMessage();
        header("Location: cart.php");
        exit();
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<section class="checkout">
    <h2>Оформление заказа</h2>
    
    <div class="checkout-summary">
        <h3>Ваш заказ</h3>
        <?php
        $total = 0;
        $stmt = $pdo->prepare("
            SELECT p.name, p.price, c.quantity 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subtotal = $row['price'] * $row['quantity'];
            $total += $subtotal;
            
            echo '<div class="checkout-item">';
            echo '<p>' . htmlspecialchars($row['name']) . ' x ' . $row['quantity'] . '</p>';
            echo '<p>' . number_format($subtotal, 2) . ' ₽</p>';
            echo '</div>';
        }
        
        echo '<div class="checkout-total">';
        echo '<h4>Итого:</h4>';
        echo '<h4>' . number_format($total, 2) . ' ₽</h4>';
        echo '</div>';
        ?>
    </div>
    
    <form action="checkout.php" method="post" class="checkout-form">
        <h3>Данные для доставки</h3>
        
        <div class="form-group">
            <label for="fullname">ФИО:</label>
            <input type="text" id="fullname" name="fullname" required>
        </div>
        
        <div class="form-group">
            <label for="address">Адрес доставки:</label>
            <textarea id="address" name="address" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="phone">Телефон:</label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        
        <div class="form-group">
            <label for="comments">Комментарий к заказу:</label>
            <textarea id="comments" name="comments"></textarea>
        </div>
        
        <button type="submit" class="btn">Подтвердить заказ</button>
    </form>
</section>

<?php require_once 'includes/footer.php'; ?>