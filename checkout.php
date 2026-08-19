<?php $pageTitle = 'Checkout';
require_once 'includes/app.php';
require_login();
$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    $_SESSION['flash'] = ['error','Your cart is empty.'];
    redirect('shop.php');
}$ids = implode(',', array_map('intval', array_keys($cart)));
$items = [];
$total = 0;
$r = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids) AND is_active=1");
while ($p = mysqli_fetch_assoc($r)) {
    $q = min((int)$cart[$p['id']], $p['stock']);
    if ($q) {
        $p['quantity'] = $q;
        $p['subtotal'] = $q * $p['price'];
        $items[] = $p;
        $total += $p['subtotal'];
    }
}if (!$items) {
    $_SESSION['flash'] = ['error','The selected products are no longer available.'];
    redirect('cart.php');
}if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $payment = $_POST['payment'] ?? 'Cash on Delivery';
    if (!$address || !$phone || !in_array($payment, ['Cash on Delivery','bKash'], true)) {
        $_SESSION['flash'] = ['error','Enter delivery address and phone number.'];
        redirect('checkout.php');
    }mysqli_begin_transaction($conn);
    try {
        $number = 'AG'.date('ymd').str_pad((string)random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $uid = current_user()['id'];
        $stmt = mysqli_prepare($conn, 'INSERT INTO orders(customer_id,order_number,total,delivery_address,phone,payment_method) VALUES(?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'isdsss', $uid, $number, $total, $address, $phone, $payment);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception();
        }$orderId = mysqli_insert_id($conn);
        foreach ($items as $p) {
            $stmt = mysqli_prepare($conn, 'INSERT INTO order_items(order_id,product_id,farmer_id,product_name,price,quantity,subtotal) VALUES(?,?,?,?,?,?,?)');
            mysqli_stmt_bind_param($stmt, 'iiisdis', $orderId, $p['id'], $p['farmer_id'], $p['name'], $p['price'], $p['quantity'], $p['subtotal']);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception();
            }$stmt = mysqli_prepare($conn, 'UPDATE products SET stock=stock-? WHERE id=? AND stock>=?');
            mysqli_stmt_bind_param($stmt, 'iii', $p['quantity'], $p['id'], $p['quantity']);
            if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) !== 1) {
                throw new Exception();
            }
        }mysqli_commit($conn);
        $_SESSION['cart'] = [];
        $_SESSION['flash'] = ['success','Order '.$number.' has been placed successfully!'];
        redirect('my-orders.php');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['flash'] = ['error','Could not place order. Please try again.'];
        redirect('checkout.php');
    }
}require_once 'includes/header.php'; ?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Secure checkout</span>
            <h1>Where should we deliver?</h1>
        </div>
    </div><?=flash()?><div class="checkout-layout">
        <form method="post" class="form-card">
            <h2>Delivery details</h2>
            <div class="form-grid">
                <div class="form-group full"><label>Full delivery address *</label><textarea name="address" rows="3" required><?=e(current_user()['address'] ?? '')?></textarea></div>
                <div class="form-group"><label>Mobile number *</label><input name="phone" required value="<?=e(current_user()['phone'] ?? '')?>"></div>
                <div class="form-group"><label>Payment method *</label><select name="payment">
                        <option>Cash on Delivery</option>
                        <option>bKash</option>
                    </select></div>
            </div><button class="primary-btn checkout-btn">Place order · ৳<?=number_format($total, 0)?></button>
        </form>
        <aside class="order-summary">
            <h2>Your items</h2><?php foreach ($items as $p):?><div><span><?=$p['emoji']?> <?=e($p['name'])?> × <?=$p['quantity']?></span><strong>৳<?=number_format($p['subtotal'], 0)?></strong></div><?php endforeach;?><div class="summary-total"><span>Total</span><strong>৳<?=number_format($total, 0)?></strong></div>
        </aside>
    </div>
</section><?php require_once 'includes/footer.php'; ?>
