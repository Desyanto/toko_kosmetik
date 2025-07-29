<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
if (!isAdmin()) die("Akses ditolak");

// HAPUS PRODUK
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: list.php?deleted=1");
}

// QUERY PRODUK
$products = $pdo->query("SELECT * FROM products")->fetchAll();
?>

<!-- TABEL PRODUK -->
<table>
    <tr>
        <th>Nama</th>
        <th>Harga</th>
        <th>Aksi</th>
    </tr>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?= $product['name'] ?></td>
        <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
        <td>
            <a href="edit.php?id=<?= $product['id'] ?>">Edit</a>
            <a href="?delete=<?= $product['id'] ?>" onclick="return confirm('Hapus produk?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>