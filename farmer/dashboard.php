<?php
$pageTitle = 'Farmer dashboard';
require_once '../includes/app.php';
require_farmer();
$id = (int)current_user()['id'];
$stats = [];
foreach (['products' => 'SELECT COUNT(*) n FROM products WHERE farmer_id='.$id,'stock' => 'SELECT COALESCE(SUM(stock),0) n FROM products WHERE farmer_id='.$id,'orders' => 'SELECT COUNT(DISTINCT order_id) n FROM order_items WHERE farmer_id='.$id,'sales' => 'SELECT COALESCE(SUM(subtotal),0) n FROM order_items WHERE farmer_id='.$id] as $key => $sql) {
    $stats[$key] = mysqli_fetch_assoc(mysqli_query($conn, $sql))['n'];
}
$recent = mysqli_query($conn, "SELECT oi.*,o.order_number,o.status,o.created_at,u.name customer FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN users u ON u.id=o.customer_id WHERE oi.farmer_id=$id ORDER BY oi.id DESC LIMIT 8");
require_once '../includes/header.php';
?>
<section class="container section-space">
    <div class="page-header">
        <div><span class="eyebrow">Farmer center</span>
            <h1>Hello, <?=e(current_user()['name'])?> 👋</h1>
            <p>Here is how your farm shop is doing today.</p>
        </div>
        <div class="button-row"><a class="secondary-btn" href="reviews.php">Latest reviews</a><a class="secondary-btn" href="users.php">Manage farmer users</a><a class="primary-btn" href="products.php?add=1">+ Add product</a></div>
    </div><?=flash()?>
    <div class="stats-grid farmer-stats">
        <div class="stat-card"><span>🥬</span>
            <div><small>Products</small><strong><?=$stats['products']?></strong></div>
        </div>
        <div class="stat-card"><span>📦</span>
            <div><small>Items in stock</small><strong><?=$stats['stock']?></strong></div>
        </div>
        <div class="stat-card"><span>🛒</span>
            <div><small>Orders received</small><strong><?=$stats['orders']?></strong></div>
        </div>
        <div class="stat-card"><span>৳</span>
            <div><small>Sales value</small><strong><?=number_format($stats['sales'], 0)?></strong></div>
        </div>
    </div>
    <div class="table-card">
        <div class="table-header">
            <div>
                <h2>Recent sales</h2>
                <p>Orders containing your products</p>
            </div><a class="text-btn" href="orders.php">Manage orders →</a>
        </div>
        <div class="table-responsive">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody><?php if (mysqli_num_rows($recent)):while ($o = mysqli_fetch_assoc($recent)):?><tr>
                        <td><?=e($o['order_number'])?><br><small><?=date('d M', strtotime($o['created_at']))?></small></td>
                        <td><?=e($o['customer'])?></td>
                        <td><?=e($o['product_name'])?> × <?=$o['quantity']?></td>
                        <td>৳<?=number_format($o['subtotal'], 0)?></td>
                        <td><span class="status status-<?=strtolower(str_replace(' ', '-', $o['status']))?>"><?=e($o['status'])?></span></td>
                    </tr><?php endwhile;
else:?><tr>
                        <td colspan="5" class="empty">No customer orders yet.</td>
                    </tr><?php endif;?></tbody>
            </table>
        </div>
    </div>
</section>
<?php require_once '../includes/footer.php'; ?>
