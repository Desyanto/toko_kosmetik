<?php
// KONEKSI DATABASE (Menggunakan PDO untuk keamanan)
$host = 'localhost';      // Server database
$db   = 'kosmetik_db';    // Nama database
$user = 'root';           // Username (default XAMPP)
$pass = '';               // Password (default XAMPP kosong)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>