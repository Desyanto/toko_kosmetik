<?php
session_start();

// CEK USER LOGIN
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// CEK ROLE ADMIN
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// REDIRECT JIKA BELUM LOGIN
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /user/login.php");
        exit;
    }
}

// REDIRECT JIKA BUKAN ADMIN
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        die("Akses hanya untuk admin");
    }
}
?>