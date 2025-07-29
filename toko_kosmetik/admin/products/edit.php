<?php
require_once '../../../includes/config.php';
require_once '../../../includes/auth.php';

if (!isAdmin()) die("Akses ditolak");

// AMBIL DATA PRODUK
$id = $_GET['id'] ?? die("ID tidak valid");
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// PROSES UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    // JIKA ADA GAMBAR BARU
    if ($_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        $target = "../../../assets/images/products/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    } else {
        $image = $product['image'];
    }

    $stmt = $pdo->prepare("
        UPDATE products SET 
        name = ?, price = ?, stock = ?, 
        description = ?, image = ? 
        WHERE id = ?
    ");
    $stmt->execute([$name, $price, $stock, $description, $image, $id]);
    header("Location: ../list.php?updated=1");
    exit;
}
?>

<!-- FORM EDIT -->
<?php include '../../../includes/header.php'; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" value="<?= $product['name'] ?>" required>
    <input type="number" name="price" value="<?= $product['price'] ?>" required>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" required>
    <textarea name="description"><?= $product['description'] ?></textarea>
    <img src="../../../assets/images/products/<?= $product['image'] ?>" height="100">
    <input type="file" name="image">
    <button type="submit">Simpan Perubahan</button>
</form>
<?php include '../../../includes/footer.php'; ?>