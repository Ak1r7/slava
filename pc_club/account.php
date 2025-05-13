<?php require_once 'includes/config.php'; ?>
<?php
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}
?>
<?php require_once 'includes/header.php'; ?>

<section class="account">
    <h2>Личный кабинет</h2>
    
    <?php if (isset($_SESSION['order_success'])): ?>
        <div class="alert success"><?php echo $_SESSION['order_success']; unset($_SESSION['order_success']); ?></div>
    <?php endif; ?>
    
    <div class="account-info">
        <h3>Привет, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
        <p>Email: <?php 
            $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            echo htmlspecialchars($user['email']); 
        ?></p>
        <p>Дата регистрации: <?php 
            $stmt = $pdo->prepare("SELECT created_at FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            echo date('d.m.Y', strtotime($user['created_at'])); 
        ?></p>
    </div>
    
    <div class="account-orders">
        <h3>Ваши заказы</h3>
        
        <?php
        $stmt = $pdo->prepare("
            SELECT o.id, o.order_date, o.total_amount, o.status 
            FROM orders o 
            WHERE o.user_id = ? 
            ORDER BY o.order_date DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            echo '<table>';
            echo '<thead><tr><th>№</th><th>Дата</th><th>Сумма</th><th>Статус</th><th>Действия</th></tr></thead>';
            echo '<tbody>';
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . date('d.m.Y H:i', strtotime($row['order_date'])) . '</td>';
                echo '<td>' . number_format($row['total_amount'], 2) . ' ₽</td>';
                echo '<td>' . htmlspecialchars($row['status']) . '</td>';
                echo '<td><a href="order.php?id=' . $row['id'] . '" class="btn">Подробнее</a></td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        } else {
            echo '<p>У вас пока нет заказов.</p>';
            echo '<a href="products.php" class="btn">Перейти к покупкам</a>';
        }
        ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>