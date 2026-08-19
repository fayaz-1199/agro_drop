<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function redirect($path)
{
    header('Location: /agro_drop/'.$path);
    exit;
}
function current_user()
{
    return $_SESSION['user'] ?? null;
}
function is_farmer()
{
    return current_user() && in_array(current_user()['role'], ['farmer','admin'], true);
}
function require_login()
{
    if (!current_user()) {
        $_SESSION['flash'] = ['error','Please log in to continue.'];
        redirect('auth/login.php');
    }
}
function require_farmer()
{
    require_login();
    if (!is_farmer()) {
        $_SESSION['flash'] = ['error','Farmer access is required.'];
        redirect('index.php');
    }
}
function require_customer()
{
    require_login();
    if (current_user()['role'] !== 'customer') {
        $_SESSION['flash'] = ['error','Customer access is required.'];
        redirect('index.php');
    }
}
function flash()
{
    if (!empty($_SESSION['flash'])) {
        [$type,$message] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="alert alert-'.$type.'">'.e($message).'</div>';
    } return '';
}
