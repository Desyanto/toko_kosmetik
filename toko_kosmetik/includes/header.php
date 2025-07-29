<!DOCTYPE html>
<html>
<head>
    <title>Toko Kosmetik</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="/">Beranda</a>
            <?php if (isLoggedIn()): ?>
                <a href="/cart">Keranjang</a>
                <a href="/user/logout.php">Logout</a>
            <?php else: ?>
                <a href="/user/login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>