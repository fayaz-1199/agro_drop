<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? 'Agro Drop';

$baseUrl = '/agro_drop';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars($pageTitle) ?> - Agro Drop
    </title>

    <link
        rel="stylesheet"
        href="<?= $baseUrl ?>/style.css"
    >

</head>

<body>

<header class="main-header">

    <div class="container header-content">

        <!-- Logo -->

        <a
            href="<?= $baseUrl ?>/index.php"
            class="logo"
        >

            <span class="logo-icon">
                🌱
            </span>

            <span class="logo-text">
                Agro <strong>Drop</strong>
            </span>

        </a>


        <!-- Menu -->

        <nav class="navbar">

            <a
                href="<?= $baseUrl ?>/index.php"
                class="nav-link"
            >
                Home
            </a>

            <a
                href="<?= $baseUrl ?>/customer/index.php"
                class="nav-link"
            >
                Customer
            </a>

            <a
                href="<?= $baseUrl ?>/product/index.php"
                class="nav-link"
            >
                Product
            </a>

            <a
                href="<?= $baseUrl ?>/order/index.php"
                class="nav-link"
            >
                Order
            </a>

            <a
                href="<?= $baseUrl ?>/review/index.php"
                class="nav-link"
            >
                Review
            </a>

        </nav>


        <!-- Right Side -->

        <div class="header-actions">

            <a
                href="#"
                class="cart-btn"
            >
                🛒

                <span class="cart-count">
                    0
                </span>
            </a>

            <?php if (isset($_SESSION['customer_id'])): ?>

                <span class="customer-name">
                    Hi,
                    <?= htmlspecialchars($_SESSION['customer_name']) ?>
                </span>

                <a
                    href="<?= $baseUrl ?>/customer/logout.php"
                    class="logout-btn"
                >
                    Logout
                </a>

            <?php else: ?>

                <a
                    href="<?= $baseUrl ?>/customer/login.php"
                    class="login-btn"
                >
                    Login
                </a>

                <a
                    href="<?= $baseUrl ?>/customer/register.php"
                    class="register-btn"
                >
                    Register
                </a>

            <?php endif; ?>

        </div>

    </div>

</header>

<main>