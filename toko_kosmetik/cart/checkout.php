<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// CEK LOGIN USER
if (!isLoggedIn()) {
  header("Location: ../user/login.php");
  exit;
}

// PROSES CHECKOUT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $userId = $_SESSION['user_id'];
  $total = 0;
  
  // HITUNG TOTAL + VALIDASI STOK
  foreach ($_SESSION['cart'] as $productId => $item) {
    $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if ($product['stock'] < $item['qty']) {
      die("Stok produk {$item['name']} tidak mencukupi!");
    }
    $total += $product['price'] * $item['qty'];
  }

  // SIMPAN ORDER (Menggunakan TRANSACTION untuk atomicity)
  $pdo->beginTransaction();
  try {
    // 1. Simpan data order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
    $stmt->execute([$userId, $total]);
    $orderId = $pdo->lastInsertId();
    
    // 2. Simpan item order + kurangi stok
    foreach ($_SESSION['cart'] as $productId => $item) {
      $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty) VALUES (?, ?, ?)");
      $stmt->execute([$orderId, $productId, $item['qty']]);
      
      $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
      $stmt->execute([$item['qty'], $productId]);
    }
    
    $pdo->commit();
    unset($_SESSION['cart']); // Kosongkan keranjang
    header("Location: success.php?order_id=$orderId");
  } catch (Exception $e) {
    $pdo->rollBack();
    die("Checkout gagal: " . $e->getMessage());
  }
}
?>