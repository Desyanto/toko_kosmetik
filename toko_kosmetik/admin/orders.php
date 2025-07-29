<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// CEK ADMIN
if (!isAdmin()) {
    header("Location: ../user/login.php");
    exit;
}

// QUERY PESANAN
$orders = $pdo->query("
    SELECT orders.*, users.name as customer 
    FROM orders 
    JOIN users ON orders.user_id = users.id
    ORDER BY created_at DESC
")->fetchAll();
?>

<!-- TAMPILAN HTML -->
<?php include '../includes/header.php'; ?>
<h2>Daftar Pesanan</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php foreach ($orders as $order): ?>
    <tr>
        <td><?= $order['id'] ?></td>
        <td><?= $order['customer'] ?></td>
        <td>Rp <?= number_format($order['total'], 0, ',', '.') ?></td>
        <td><?= ucfirst($order['status']) ?></td>
        <td>
            <a href="orders/detail.php?id=<?= $order['id'] ?>">Detail</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php include '../includes/footer.php'; ?>