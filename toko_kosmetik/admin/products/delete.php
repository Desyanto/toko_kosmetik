<?php
require_once '../../../includes/config.php';
require_once '../../../includes/auth.php';

if (!isAdmin()) die("Akses ditolak");

// HAPUS PRODUK
$id = $_GET['id'] ?? die("ID tidak valid");
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../list.php?deleted=1");
exit;
?>