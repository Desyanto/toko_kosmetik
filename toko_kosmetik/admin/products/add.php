<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

// CEK APAKAH USER ADALAH ADMIN
if (!isAdmin()) {
    header("Location: ../login.php"); // Redirect jika bukan admin
    exit;
}

// TAMBAH PRODUK BARU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // UPLOAD GAMBAR
    $image = $_FILES['image']['name'];
    $target = "../../assets/images/products/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    // SIMPAN KE DATABASE
    $stmt = $pdo->prepare("INSERT INTO products (name, price, stock, description, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $stock, $description, $image]);
    header("Location: list.php?success=1"); // Redirect ke daftar produk
    exit;
}
?>

<!-- FORM TAMBAH PRODUK -->
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="admin-form">
        <h2>Tambah Produk Baru</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nama Produk" required>
            <input type="number" name="price" placeholder="Harga" required>
            <input type="number" name="stock" placeholder="Stok" required>
            <textarea name="description" placeholder="Deskripsi"></textarea>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Simpan</button>
        </form>
    </div>
</body>
</html>