<?php
require_once __DIR__ . '/app.php';
$pageTitle = $pageTitle ?? 'AgroDrop';
$baseUrl = '/agro_drop';
$cartCount = array_sum($_SESSION['cart'] ?? []);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($pageTitle) ?> | AgroDrop</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/style.css">
</head>

<body>
    <header class="main-header">
        <div class="container header-content"><a href="<?= $baseUrl ?>/index.php" class="logo">🌱 <span>Agro<strong>Drop</strong></span></a>
            <nav class="navbar"><a href="<?= $baseUrl ?>/shop.php">Shop</a><a href="<?= $baseUrl ?>/shop.php?category=Vegetables">Vegetables</a><a href="<?= $baseUrl ?>/shop.php?category=Fruits">Fruits</a><?php if (is_farmer()): ?><a href="<?= $baseUrl ?>/farmer/dashboard.php">Farmer Panel</a><?php elseif (current_user()): ?><a href="<?= $baseUrl ?>/review/index.php">My Reviews</a><?php endif; ?></nav>
            <div class="header-actions"><a class="cart-link" href="<?= $baseUrl ?>/cart.php">🛒 <span><?= $cartCount ?></span></a><?php if (current_user()): ?><a class="account-link" href="<?= $baseUrl ?>/my-orders.php">Hi, <?= e(explode(' ', current_user()['name'])[0]) ?></a><a class="login-btn" href="<?= $baseUrl ?>/auth/logout.php">Logout</a><?php else: ?><a class="login-btn" href="<?= $baseUrl ?>/auth/login.php">Login</a><a class="primary-btn header-cta" href="<?= $baseUrl ?>/auth/register.php">Join AgroDrop</a><?php endif; ?></div>
        </div>
    </header>
    <main>
