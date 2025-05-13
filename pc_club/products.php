<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<section class="products">
    <h2>Наши устройства</h2>
    
    <div class="filters">
        <form method="get">
            <select name="category">
                <option value="">Все категории</option>
                <?php
                $stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = (isset($_GET['category']) && $_GET['category'] == $row['category']) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($row['category']) . '" ' . $selected . '>' . htmlspecialchars($row['category']) . '</option>';
                }
                ?>
            </select>
            <input type="text" name="search" placeholder="Поиск..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn">Фильтровать</button>
        </form>
    </div>
    
    <div class="products-grid">
        <?php
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $sql .= " AND category = ?";
            $params[] = $_GET['category'];
        }
        
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $search_term = '%' . $_GET['search'] . '%';
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="product-card">';
                echo '<img src="assets/images/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                echo '<p class="price">' . number_format($row['price'], 2) . ' ₽</p>';
                echo '<p class="stock">' . ($row['stock'] > 0 ? 'В наличии' : 'Нет в наличии') . '</p>';
                echo '<a href="product.php?id=' . $row['id'] . '" class="btn">Подробнее</a>';
                if (isLoggedIn() && $row['stock'] > 0) {
                    echo '<form action="add_to_cart.php" method="post" class="add-to-cart-form">';
                    echo '<input type="hidden" name="product_id" value="' . $row['id'] . '">';
                    echo '<input type="number" name="quantity" value="1" min="1" max="' . $row['stock'] . '">';
                    echo '<button type="submit" class="btn">В корзину</button>';
                    echo '</form>';
                }
                echo '</div>';
            }
        } else {
            echo '<p>Товары не найдены.</p>';
        }
        ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>