<?php $pageTitle = 'Farmer orders';
require_once '../includes/app.php';
require_farmer();
$farmer = current_user()['id'];
if (isset($_GET['id'],$_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];
    if (in_array($status, ['Confirmed','Packed','On the way','Delivered'], true)) {
        $stmt = mysqli_prepare($conn, 'UPDATE orders o JOIN order_items oi ON oi.order_id=o.id SET o.status=? WHERE o.id=? AND oi.farmer_id=?');
        mysqli_stmt_bind_param($stmt, 'sii', $status, $id, $farmer);
        mysqli_stmt_execute($stmt);
        $_SESSION['flash'] = ['success','Order status updated.'];
    }redirect('farmer/orders.php');
}$orders = mysqli_query($conn, "SELECT DISTINCT o.*,u.name customer FROM orders o JOIN order_items oi ON oi.order_id=o.id JOIN users u ON u.id=o.customer_id WHERE oi.farmer_id=$farmer ORDER BY o.id DESC");
require_once '../includes/header.php'; ?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Farmer center</span>
            <h1>Customer orders</h1>
            <p>Update orders as you prepare them for delivery.</p>
        </div><a class="secondary-btn" href="dashboard.php">← Dashboard</a>
    </div><?=flash()?><div class="table-card">
        <div class="table-responsive">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Delivery</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody><?php while ($o = mysqli_fetch_assoc($orders)):?><tr>
                        <td><strong><?=e($o['order_number'])?></strong><br><small><?=date('d M Y', strtotime($o['created_at']))?></small></td>
                        <td><?=e($o['customer'])?><br><small><?=e($o['phone'])?></small></td>
                        <td><?=e($o['delivery_address'])?></td>
                        <td><?=e($o['payment_method'])?></td>
                        <td>৳<?=number_format($o['total'], 0)?></td>
                        <td>
                            <form><input type="hidden" name="id" value="<?=$o['id']?>"><select name="status" onchange="this.form.submit()">
                                    <option disabled selected><?=e($o['status'])?></option><?php foreach (['Confirmed','Packed','On the way','Delivered'] as $s):?><option><?=$s?></option><?php endforeach;?>
                                </select></form>
                        </td>
                    </tr><?php endwhile;?></tbody>
            </table>
        </div>
    </div>
</section><?php require_once '../includes/footer.php'; ?>
