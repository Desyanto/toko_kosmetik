<?php
session_start();  // Mulai session
require_once '../includes/config.php';  // Load koneksi database
require_once '../includes/auth.php';    // Load fungsi validasi

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek apakah email dan password terisi
    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi!";
    } else {
        // Query ke database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verifikasi password (compare input dengan hash di database)
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];  // Simpan ID user di session
            header("Location: ../index.php");   // Redirect ke homepage
            exit;
        } else {
            $error = "Email atau password salah!";
        }
    }
}
?>

<!-- Tampilan HTML -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">  <!-- Load CSS -->
</head>
<body>
    <form method="POST">
        <input type="email" name="email" placeholder="Email">
        <input type="password" name="password" placeholder="Password">
        <button type="submit">Login</button>
        <?php if (isset($error)) { echo "<p>$error</p>"; } ?>  <!-- Tampilkan error -->
    </form>
</body>
</html>