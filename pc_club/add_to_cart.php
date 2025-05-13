<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    $_SESSION['cart_error'] = "Для добавления товаров в корзину необходимо войти в систему";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product || $product['stock'] < $quantity) {
        $_SESSION['cart_error'] = "Товар недоступен в указанном количестве";
        header("Location: products.php");
        exit();
    }
    
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $product_id]);
    $cart_item = $stmt->fetch();
    
    if ($cart_item) {
        $new_quantity = $cart_item['quantity'] + $quantity;
        if ($new_quantity > $product['stock']) {
            $_SESSION['cart_error'] = "Нельзя добавить больше товара, чем есть в наличии";
            header("Location: products.php");
            exit();
        }
        
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $cart_item['id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
    }
    
    $_SESSION['cart_success'] = "Товар успешно добавлен в корзину";
    header("Location: cart.php");
    exit();
}

header("Location: products.php");
exit();
?>