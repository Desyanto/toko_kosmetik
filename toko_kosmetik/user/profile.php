<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

// AMBIL DATA USER
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// UPDATE PROFILE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $_SESSION['user_id']]);
    header("Location: profile.php?updated=1");
    exit;
}
?>

<!-- TAMPILAN PROFILE -->
<?php include '../includes/header.php'; ?>
<form method="POST">
    <input type="text" name="name" value="<?= $user['name'] ?>" required>
    <input type="email" name="email" value="<?= $user['email'] ?>" required>
    <button type="submit">Update Profil</button>
</form>
<a href="change-password.php">Ganti Password</a>
<?php include '../includes/footer.php'; ?>