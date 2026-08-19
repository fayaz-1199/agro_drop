<?php require_once '../includes/app.php';
if (current_user()) {
    redirect('index.php');
}if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'customer';
    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$phone || strlen($password) < 6 || !in_array($role, ['customer','farmer'], true)) {
        $_SESSION['flash'] = ['error','Complete all fields; password must be at least 6 characters.'];
        redirect('auth/register.php');
    }$hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, 'INSERT INTO users(name,email,phone,password,role) VALUES(?,?,?,?,?)');
    mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $hash, $role);
    if (!mysqli_stmt_execute($stmt)) {
        $_SESSION['flash'] = ['error','This email is already registered.'];
        redirect('auth/register.php');
    }$id = mysqli_insert_id($conn);
    $_SESSION['user'] = ['id' => $id,'name' => $name,'email' => $email,'phone' => $phone,'role' => $role,'address' => ''];
    $_SESSION['flash'] = ['success','Your account is ready. Welcome to AgroDrop!'];
    redirect($role === 'farmer' ? 'farmer/dashboard.php' : 'shop.php');
}$pageTitle = 'Create account';
require_once '../includes/header.php'; ?>
<section class="auth-wrap">
    <div class="auth-panel"><span class="hero-badge">Join the marketplace</span>
        <h1>Create your account</h1>
        <p>Shop fresh food, or open your digital farm shop.</p><?=flash()?><form method="post" class="auth-form"><label>Full name<input name="name" required></label><label>Email address<input type="email" name="email" required></label><label>Mobile number<input name="phone" required></label><label>I want to<input type="hidden" name="role" id="role" value="<?=($_GET['role'] ?? '') === 'farmer' ? 'farmer' : 'customer'?>">
                <div class="role-choice"><button type="button" data-role="customer">Buy products</button><button type="button" data-role="farmer">Sell products</button></div>
            </label><label>Password<input type="password" name="password" minlength="6" required></label><button class="primary-btn checkout-btn">Create account</button></form>
        <p class="auth-switch">Already registered? <a href="login.php">Log in</a></p>
    </div>
</section>
<script>
    const field = document.getElementById('role'),
        buttons = document.querySelectorAll('[data-role]');

    function setRole(v) {
        field.value = v;
        buttons.forEach(b => b.classList.toggle('picked', b.dataset.role === v))
    }
    buttons.forEach(b => b.onclick = () => setRole(b.dataset.role));
    setRole(field.value)

</script><?php require_once '../includes/footer.php'; ?>
