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
    <link rel="stylesheet" href="payment_form/style/style.css">
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
<div class="card-form">
        <div class="card-display">
            <div class="card">
                <div class="logo-cards">
                    <img src="img/chip.png" alt="" class="chip">
                    <div>
                        <div>
                            <img src="payment_form/img/card-type/mastercard.png" class="brand" alt="Card Brand" id="card-brand">
                            <img src="payment_form/img/card-type/visa.png" class="brand" alt="Card Brand" id="card-brand">
                            <img src="payment_form/img/card-type/discover.png" class="brand" alt="Card Brand" id="card-brand">
                            <img src="payment_form/img/card-type/amex.png" class="brand" alt="Card Brand" id="card-brand">    
                        </div>
                    </div>
                </div>
                <div class="card-number" id="card-number">
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div id="space"></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div id="space"></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div id="space"></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                    <div class="digit"><span>#</span></div>
                </div>
                <div class="card-details">
                    <div class="holder" id="holder">
                        <span class="card-subtitle">Card Holder</span>
                        <span class="card-holder" id="card-holder">NUME PRENUME</span>
                    </div>
                    <div class="expiry" id="expiry">
                        <span class="card-subtitle">Expires</span>
                        <span class="card-expiry" id="card-expiry">MM/YY</span>
                    </div>
                </div>
            </div>
            <div class="cardBack">
                <div id="black-line"></div>
                <div id="cvv">
                    <h5>CVV</h5>
                    <h2>123</h2>
                </div>
                <img src="img/card-type/discover.png" class="card-brand-rev" alt="">
            </div>

        </div>
        <div class="container">
            <form class="card-form" id="card-form">
                <div class="form-group">
                    <label for="card-number-input">Card Number</label>
                    <input type="text" id="card-number-input" maxlength="16" placeholder="1234 5678 9012 3456">
                </div>
                <div class="form-group">
                    <label for="card-holder-input">Card Name</label>
                    <input type="text" id="card-holder-input" maxlength="23" placeholder="NUME PRENUME">
                </div>
                <div class="form-group">
                    <div class="card-info">
                        <label for="card-expiry-month">Expiration Date</label>
                        <label class="cvv" for="card-cvv-input">CVV</label>
                    </div>
                    <div class="expiry-group">
                        <select id="card-expiry-month">
                            <option value="MM" selected disabled>Month</option>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                        <select id="card-expiry-year">
                            <option value="YY" selected disabled>Year</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                            <option value="2031">2031</option>
                            <option value="2032">2032</option>
                            <option value="2033">2033</option>
                            <option value="2034">2034</option>
                            <option value="2035">2035</option>

                        </select>
                        <div>
                            <input class="card-cvv" type="text" id="card-cvv-input" maxlength="3" placeholder="123">
                        </div>
                    </div>
                </div>
                        <button type="submit" class="btn">Подтвердить заказ</button>
            </form>
        </div>
    </div>

    </form>
    <script src="payment_form/script/script.js"></script>
</section> 

<?php require_once 'includes/footer.php'; ?>