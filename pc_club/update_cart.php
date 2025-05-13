<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];
    
    $stmt = $pdo->prepare("
        SELECT c.id, p.stock 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.id = ? AND c.user_id = ?
    ");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    $cart_item = $stmt->fetch();
    
    if (!$cart_item) {
        $_SESSION['cart_error'] = "Товар не найден в вашей корзине";
        header("Location: cart.php");
        exit();
    }
    
    if ($quantity <= 0) {
        $_SESSION['cart_error'] = "Количество должно быть больше нуля";
        header("Location: cart.php");
        exit();
    }
    
    if ($quantity > $cart_item['stock']) {
        $_SESSION['cart_error'] = "Нельзя добавить больше товара, чем есть в наличии";
        header("Location: cart.php");
        exit();
    }
    
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$quantity, $cart_id]);
    
    $_SESSION['cart_success'] = "Корзина обновлена";
    header("Location: cart.php");
    exit();
}

header("Location: cart.php");
exit();
?>