<?php
$pageTitle = 'Farmer users';
require_once '../includes/app.php';
require_farmer();

function farmerUserGo($message, $error = false)
{
    $_SESSION['flash'] = [$error ? 'error' : 'success', $message];
    redirect('farmer/users.php');
}

$ownerId = (int) current_user()['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $phone === '' || strlen($password) < 6) {
        farmerUserGo('Complete all fields with a valid email and a password of at least 6 characters.', true);
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'farmer';
    $create = mysqli_prepare($conn, 'INSERT INTO users(name,email,phone,password,role,address,created_by_farmer_id) VALUES(?,?,?,?,?,?,?)');
    mysqli_stmt_bind_param($create, 'ssssssi', $name, $email, $phone, $hash, $role, $address, $ownerId);
    if (!mysqli_stmt_execute($create)) {
        farmerUserGo('This email is already registered. Use a different email.', true);
    }
    farmerUserGo('New farmer user created successfully.');
}

$users = mysqli_prepare($conn, "SELECT id,name,email,phone,address,created_at FROM users WHERE created_by_farmer_id=? AND role='farmer' ORDER BY id DESC");
mysqli_stmt_bind_param($users, 'i', $ownerId);
mysqli_stmt_execute($users);
$userList = mysqli_stmt_get_result($users);
require_once '../includes/header.php';
?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Farmer center</span>
            <h1>Farmer users</h1>
            <p>Create and keep track of farmer accounts created under your farm.</p>
        </div><a class="secondary-btn" href="dashboard.php">← Dashboard</a>
    </div>
    <?= flash() ?>
    <div class="form-card">
        <h2>Create farmer user</h2>
        <form method="post" class="form-grid">
            <div class="form-group"><label>Full name *</label><input name="name" maxlength="100" required></div>
            <div class="form-group"><label>Email address *</label><input type="email" name="email" maxlength="120" required></div>
            <div class="form-group"><label>Mobile number *</label><input name="phone" maxlength="25" required></div>
            <div class="form-group"><label>Password *</label><input type="password" name="password" minlength="6" required></div>
            <div class="form-group full"><label>Farm address</label><input name="address" maxlength="255"></div>
            <div class="form-actions full"><button class="primary-btn">Create farmer user</button></div>
        </form>
    </div>
    <div class="table-card users-table">
        <div class="table-header">
            <div>
                <h2>Users created by you</h2>
                <p>These farmer accounts are linked to your farm account.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody><?php if (mysqli_num_rows($userList)): while ($user = mysqli_fetch_assoc($userList)): ?><tr>
                        <td><strong><?= e($user['name']) ?></strong></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['phone']) ?></td>
                        <td><?= e($user['address']) ?></td>
                        <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                    </tr><?php endwhile;
    else: ?><tr>
                        <td colspan="5" class="empty">No farmer users created yet.</td>
                    </tr><?php endif; ?></tbody>
            </table>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>
