<?php 
require_once 'includes/config.php';
require_once 'includes/header.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// [Păstrează logica existentă de verificare coș]
?>

<section class="checkout-container">
    <!-- Încarcă stilurile formularului -->
    <link rel="stylesheet" href="/payment_form/assets/css/style.css">
    
    <div class="order-summary">
        <h2>Ваш заказ</h2>
        <?php
        // [Păstrează logica afișării coșului]
        ?>
    </div>

    <div class="payment-section">
        <h2>Данные для доставки</h2>
        
        <?php
        // Include formularul de plată
        $payment_form = $_SERVER['DOCUMENT_ROOT'].'/payment_form/index.php';
        if(file_exists($payment_form)) {
            include $payment_form;
        } else {
            echo '<div class="error">Форма оплаты временно недоступна</div>';
        }
        ?>
    </div>
</section>

<!-- Încarcă JavaScript-ul la sfârșit -->
<script src="/payment_form/assets/js/script.js"></script>

<?php require_once 'includes/footer.php'; ?>