<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = $_POST['cart_id'];
    
    $stmt = $pdo->prepare("SELECT id FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    $cart_item = $stmt->fetch();
    
    if ($cart_item) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$cart_id]);
        
        $_SESSION['cart_success'] = "Товар удален из корзины";
    } else {
        $_SESSION['cart_error'] = "Товар не найден в вашей корзине";
    }
}

header("Location: cart.php");
exit();
?>