<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// CEK ROLE ADMIN
if (!isAdmin()) {
  header("Location: ../user/login.php");
  exit;
}

// STATISTIK
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue = $pdo->query("SELECT SUM(total) FROM orders")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
  <div class="admin-container">
    <h1>Dashboard Admin</h1>
    <div class="stats">
      <div class="stat-card">
        <h3>Total Produk</h3>
        <p><?= $totalProducts ?></p>
      </div>
      <div class="stat-card">
        <h3>Total Pesanan</h3>
        <p><?= $totalOrders ?></p>
      </div>
      <div class="stat-card">
        <h3>Total Pendapatan</h3>
        <p>Rp <?= number_format($revenue, 0, ',', '.') ?></p>
      </div>
    </div>
    <a href="products/list.php" class="btn">Kelola Produk</a>
  </div>
</body>
</html>