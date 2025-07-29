<?php
require_once '../includes/config.php';

$id = $_GET['id'] ?? die("Produk tidak ditemukan");
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}
?>

<!-- TAMPILAN DETAIL -->
<div class="product-detail">
    <img src="../assets/images/products/<?= $product['image'] ?>">
    <h1><?= $product['name'] ?></h1>
    <p class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
    <p><?= $product['description'] ?></p>
    <a href="../cart/index.php?add=<?= $product['id'] ?>" class="add-to-cart">+ Keranjang</a>
</div>