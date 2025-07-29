<?php
// CEK LOGIN USER
function isLoggedIn() {
  return isset($_SESSION['user_id']);
}

// CEK ROLE ADMIN
function isAdmin() {
  return isLoggedIn() && $_SESSION['role'] == 'admin';
}

// FORMAT RUPIAH
function rupiah($number) {
  return 'Rp ' . number_format($number, 0, ',', '.');
}

// REDIRECT DENGAN PESAN
function redirect($url, $message = null) {
  if ($message) {
    $_SESSION['flash_message'] = $message;
  }
  header("Location: $url");
  exit;
}
?>