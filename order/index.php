<?php
$pageTitle = 'Orders';
require_once '../config/database.php';
function goOrder($message, $error = false)
{
    header('Location: index.php?'.($error ? 'error' : 'success').'='.urlencode($message));
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = (int)($_POST['customer_id'] ?? 0);
    $product = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    $address = trim($_POST['delivery_address'] ?? '');
    $r = mysqli_query($conn, "SELECT price,stock FROM products WHERE id=$product AND status=1");
    $item = mysqli_fetch_assoc($r);
    if (!$customer || !$item || $quantity < 1 || !$address) {
        goOrder('Select a customer and active product, then enter quantity and address.', true);
    }
    if ($quantity > $item['stock']) {
        goOrder('Only '.$item['stock'].' item(s) are available in stock.', true);
    }
    $price = (float)$item['price'];
    $total = $price * $quantity;
    mysqli_begin_transaction($conn);
    try {
        $stmt = mysqli_prepare($conn, 'INSERT INTO orders (customer_id,product_id,quantity,unit_price,total,delivery_address) VALUES (?,?,?,?,?,?)');
        mysqli_stmt_bind_param($stmt, 'iiidds', $customer, $product, $quantity, $price, $total, $address);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception();
        }$stmt = mysqli_prepare($conn, 'UPDATE products SET stock=stock-? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'ii', $quantity, $product);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception();
        }mysqli_commit($conn);
        goOrder('Order created successfully. Stock updated.');
    } catch (Exception $e) {
        mysqli_rollback($conn);
        goOrder('Could not create order.', true);
    }
}
if (isset($_GET['status'],$_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    if (in_array($status, ['Pending','Confirmed','Delivered','Cancelled'], true)) {
        $stmt = mysqli_prepare($conn, 'UPDATE orders SET status=? WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        mysqli_stmt_execute($stmt);
        goOrder('Order status updated.');
    }
}
$customers = mysqli_query($conn, 'SELECT id,name,address FROM customers WHERE status=1 ORDER BY name');
$products = mysqli_query($conn, 'SELECT id,name,price,stock,unit FROM products WHERE status=1 AND stock>0 ORDER BY name');
$orders = mysqli_query($conn, 'SELECT o.*,c.name customer,p.name product,p.unit FROM orders o JOIN customers c ON c.id=o.customer_id JOIN products p ON p.id=o.product_id ORDER BY o.id DESC');
require_once '../includes/header.php';
?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Sales module</span>
            <h1>Orders</h1>
            <p>Create a customer order and follow its delivery status.</p>
        </div><a class="primary-btn" href="?add=1">+ Create Order</a>
    </div><?php if (isset($_GET['success']) || isset($_GET['error'])):?><div class="alert <?=isset($_GET['error']) ? 'alert-error' : 'alert-success'?>"><?=htmlspecialchars($_GET['error'] ?? $_GET['success'])?></div><?php endif;?>
    <?php if (isset($_GET['add'])):?><div class="form-card">
        <h2>New customer order</h2>
        <form method="post" class="form-grid">
            <div class="form-group"><label>Customer *</label><select name="customer_id" required>
                    <option value="">Select customer</option><?php while ($c = mysqli_fetch_assoc($customers)):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option><?php endwhile;?>
                </select></div>
            <div class="form-group"><label>Product *</label><select name="product_id" required>
                    <option value="">Select product</option><?php while ($p = mysqli_fetch_assoc($products)):?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?> — ৳<?=$p['price']?>/<?=htmlspecialchars($p['unit'])?> (<?=$p['stock']?> left)</option><?php endwhile;?>
                </select></div>
            <div class="form-group"><label>Quantity *</label><input type="number" min="1" name="quantity" required></div>
            <div class="form-group"><label>Delivery address *</label><input name="delivery_address" required placeholder="House, road, area"></div>
            <div class="form-actions full"><a class="secondary-btn" href="index.php">Cancel</a><button class="primary-btn">Place Order</button></div>
        </form>
    </div><?php endif;?>
    <div class="table-card">
        <div class="table-header">
            <div>
                <h2>All orders</h2>
                <p>Order and delivery management</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Total</th>
                        <th>Delivery</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody><?php if (mysqli_num_rows($orders)):while ($o = mysqli_fetch_assoc($orders)):?><tr>
                        <td>#<?=$o['id']?><br><small><?=date('d M Y', strtotime($o['order_date']))?></small></td>
                        <td><?=htmlspecialchars($o['customer'])?></td>
                        <td><?=htmlspecialchars($o['product'])?><br><small><?=$o['quantity']?> <?=htmlspecialchars($o['unit'])?></small></td>
                        <td>৳<?=number_format($o['total'], 2)?></td>
                        <td><?=htmlspecialchars($o['delivery_address'])?></td>
                        <td>
                            <form method="get"><input type="hidden" name="id" value="<?=$o['id']?>"><select name="status" onchange="this.form.submit()" class="status-select">
                                    <option <?=$o['status'] === 'Pending' ? 'selected' : ''?>>Pending</option>
                                    <option <?=$o['status'] === 'Confirmed' ? 'selected' : ''?>>Confirmed</option>
                                    <option <?=$o['status'] === 'Delivered' ? 'selected' : ''?>>Delivered</option>
                                    <option <?=$o['status'] === 'Cancelled' ? 'selected' : ''?>>Cancelled</option>
                                </select></form>
                        </td>
                    </tr><?php endwhile;
else:?><tr>
                        <td colspan="6" class="empty">No orders yet.</td>
                    </tr><?php endif;?></tbody>
            </table>
        </div>
    </div>
</section><?php require_once '../includes/footer.php';?>
