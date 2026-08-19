<?php $pageTitle = 'My orders';
require_once 'includes/app.php';
require_login();
$id = current_user()['id'];
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE customer_id=$id ORDER BY id DESC");
require_once 'includes/header.php'; ?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Customer account</span>
            <h1>My orders</h1>
            <p>Track every order from confirmation to delivery.</p>
        </div><a class="primary-btn" href="shop.php">Shop again</a>
    </div><?=flash()?><div class="table-card">
        <div class="table-responsive">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Delivery address</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody><?php if (mysqli_num_rows($orders)):while ($o = mysqli_fetch_assoc($orders)):?><tr>
                        <td><strong><?=e($o['order_number'])?></strong></td>
                        <td><?=date('d M Y', strtotime($o['created_at']))?></td>
                        <td><?=e($o['delivery_address'])?></td>
                        <td><?=e($o['payment_method'])?></td>
                        <td>৳<?=number_format($o['total'], 0)?></td>
                        <td><span class="status status-<?=strtolower(str_replace(' ', '-', $o['status']))?>"><?=e($o['status'])?></span></td>
                    </tr><?php endwhile;
else:?><tr>
                        <td colspan="6" class="empty">You have not placed an order yet.</td>
                    </tr><?php endif;?></tbody>
            </table>
        </div>
    </div>
</section><?php require_once 'includes/footer.php'; ?>
