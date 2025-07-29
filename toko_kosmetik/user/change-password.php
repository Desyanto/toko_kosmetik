<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
requireLogin();

// PROSES GANTI PASSWORD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    
    // CEK PASSWORD SEKARANG
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (password_verify($current, $user['password'])) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['user_id']]);
        header("Location: profile.php?password_changed=1");
    } else {
        $error = "Password saat ini salah!";
    }
}
?>

<!-- FORM GANTI PASSWORD -->
<?php if (isset($error)): ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
    <input type="password" name="current_password" placeholder="Password Saat Ini" required>
    <input type="password" name="new_password" placeholder="Password Baru" required>
    <button type="submit">Ganti Password</button>
</form>