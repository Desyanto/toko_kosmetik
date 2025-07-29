<?php
session_start();
require_once '../includes/config.php';

// TAMBAH PRODUK KE KERANJANG
if (isset($_GET['add'])) {
    $id = $_GET['add'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $_SESSION['cart'][$id] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'qty' => ($_SESSION['cart'][$id]['qty'] ?? 0) + 1
        ];
    }
}

// HAPUS PRODUK
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
}
?>

<!-- TAMPILAN KERANJANG -->
<div class="cart-items">
    <?php foreach ($_SESSION['cart'] ?? [] as $id => $item): ?>
        <div class="item">
            <h4><?= $item['name'] ?></h4>
            <p>Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
            <p>Qty: <?= $item['qty'] ?></p>
            <a href="?remove=<?= $id ?>">Hapus</a>
        </div>
    <?php endforeach; ?>
    <a href="checkout.php" class="checkout-btn">Checkout</a>
</div>