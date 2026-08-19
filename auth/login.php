<?php require_once '../includes/app.php';
if (current_user()) {
    redirect('index.php');
}if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = mysqli_prepare($conn, 'SELECT * FROM users WHERE email=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = ['id' => $user['id'],'name' => $user['name'],'email' => $user['email'],'phone' => $user['phone'],'role' => $user['role'],'address' => $user['address']];
        $_SESSION['flash'] = ['success','Welcome back, '.$user['name'].'!'];
        redirect($user['role'] === 'farmer' ? 'farmer/dashboard.php' : 'shop.php');
    }$_SESSION['flash'] = ['error','Incorrect email or password.'];
    redirect('auth/login.php');
}$pageTitle = 'Login';
require_once '../includes/header.php'; ?>
<section class="auth-wrap">
    <div class="auth-panel"><span class="hero-badge">Welcome back</span>
        <h1>Log in to AgroDrop</h1>
        <p>Continue shopping fresh products or manage your farm.</p><?=flash()?><form method="post" class="auth-form"><label>Email address<input type="email" name="email" required></label><label>Password<input type="password" name="password" required></label><button class="primary-btn checkout-btn">Log in</button></form>
        <p class="auth-switch">New here? <a href="register.php">Create an account</a></p>
        <div class="demo-note"><strong>Demo account</strong><br>customer@agrodrop.test / password123<br>farmer@agrodrop.test / password123</div>
    </div>
</section><?php require_once '../includes/footer.php'; ?>
