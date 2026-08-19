<?php $pageTitle = 'Your cart';
require_once 'includes/app.php';
$_SESSION['cart'] = $_SESSION['cart'] ?? [];
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $qty = max(1, (int)($_GET['qty'] ?? 1));
    $r = mysqli_query($conn, "SELECT id,stock FROM products WHERE id=$id AND is_active=1");
    if ($p = mysqli_fetch_assoc($r)) {
        $_SESSION['cart'][$id] = min($p['stock'], ($_SESSION['cart'][$id] ?? 0) + $qty);
        $_SESSION['flash'] = ['success','Product added to your cart.'];
    }redirect('cart.php');
}if (isset($_POST['update'])) {
    foreach ($_POST['qty'] ?? [] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty < 1) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }$_SESSION['flash'] = ['success','Cart updated.'];
    redirect('cart.php');
}if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int)$_GET['remove']]);
    redirect('cart.php');
}$ids = array_keys($_SESSION['cart']);
$items = [];
$total = 0;
if ($ids) {
    $list = implode(',', array_map('intval', $ids));
    $r = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($list)");
    while ($p = mysqli_fetch_assoc($r)) {
        $p['quantity'] = min($_SESSION['cart'][$p['id']], $p['stock']);
        $p['subtotal'] = $p['quantity'] * $p['price'];
        $items[] = $p;
        $total += $p['subtotal'];
    }
}require_once 'includes/header.php'; ?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Your basket</span>
            <h1>Shopping cart</h1>
            <p>Review your fresh picks before checkout.</p>
        </div><a class="text-btn" href="shop.php">← Continue shopping</a>
    </div><?=flash()?><?php if ($items):?><div class="cart-layout">
        <form method="post" class="table-card"><input type="hidden" name="update" value="1">
            <div class="table-responsive">
                <table class="customer-table cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($items as $p):?><tr>
                            <td><span class="cart-emoji"><?=$p['emoji']?></span><strong><?=e($p['name'])?></strong><small><?=e($p['unit'])?></small></td>
                            <td>৳<?=number_format($p['price'], 0)?></td>
                            <td><input class="qty-input" name="qty[<?=$p['id']?>]" min="1" max="<?=$p['stock']?>" type="number" value="<?=$p['quantity']?>"></td>
                            <td><strong>৳<?=number_format($p['subtotal'], 0)?></strong></td>
                            <td><a class="danger" href="?remove=<?=$p['id']?>">Remove</a></td>
                        </tr><?php endforeach;?></tbody>
                </table>
            </div><button class="secondary-btn small-btn">Update cart</button>
        </form>
        <aside class="order-summary">
            <h2>Order summary</h2>
            <div><span>Subtotal</span><strong>৳<?=number_format($total, 0)?></strong></div>
            <div><span>Delivery</span><strong>Free</strong></div>
            <div class="summary-total"><span>Total</span><strong>৳<?=number_format($total, 0)?></strong></div><a class="primary-btn checkout-btn" href="checkout.php">Proceed to checkout →</a><small>Secure checkout · Cash on delivery available</small>
        </aside>
    </div><?php else:?><div class="empty-card">
        <h2>Your cart is empty</h2>
        <p>Add fresh products and come back here to checkout.</p><a class="primary-btn" href="shop.php">Browse products</a>
    </div><?php endif;?>
</section><?php require_once 'includes/footer.php'; ?>
