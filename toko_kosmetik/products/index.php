<?php
require_once '../includes/config.php';

// PAGINATION
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Jumlah produk per halaman
$offset = ($page - 1) * $limit;

// QUERY PRODUK
$stmt = $pdo->prepare("SELECT * FROM products LIMIT ? OFFSET ?");
$stmt->execute([$limit, $offset]);
$products = $stmt->fetchAll();

// TOTAL PRODUK (untuk pagination)
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalPages = ceil($totalProducts / $limit);
?>

<!-- TAMPILAN HTML -->
<!DOCTYPE html>
<html>
<head>
  <title>Produk Kosmetik</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="product-list">
    <?php foreach ($products as $product): ?>
      <div class="product-card">
        <img src="../assets/images/products/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
        <h3><?= $product['name'] ?></h3>
        <p>Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
        <a href="detail.php?id=<?= $product['id'] ?>">Lihat Detail</a>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- PAGINATION -->
  <div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>" <?= $i == $page ? 'class="active"' : '' ?>><?= $i ?></a>
    <?php endfor; ?>
  </div>
</body>
</html>