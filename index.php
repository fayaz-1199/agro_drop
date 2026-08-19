<?php $pageTitle = 'Fresh food from local farms';
require_once 'includes/app.php';
$featured = mysqli_query($conn, 'SELECT p.*,u.name farmer FROM products p JOIN users u ON u.id=p.farmer_id WHERE p.is_active=1 AND p.stock>0 ORDER BY p.id DESC LIMIT 4');
require_once 'includes/header.php'; ?>
<section class="hero">
    <div class="container hero-grid">
        <div><span class="hero-badge">🌿 Farm to table, made simple</span>
            <h1>Fresh food from <span>farmers you can trust.</span></h1>
            <p>Buy seasonal vegetables, grains and natural products directly from verified local farms—at fair prices.</p>
            <div class="hero-buttons"><a class="primary-btn large-btn" href="shop.php">Start Shopping →</a><a class="text-btn" href="auth/register.php?role=farmer">I am a farmer →</a></div>
            <div class="trust-row"><span>✓ Fresh harvest</span><span>✓ Fair pricing</span><span>✓ Easy delivery</span></div>
        </div>
        <div class="hero-art">
            <div class="sun">☀️</div>
            <div class="farm-card">👩🏽‍🌾<strong>Direct from farm</strong><small>Picked with care today</small></div>
            <div class="produce produce-one">🥕</div>
            <div class="produce produce-two">🥬</div>
            <div class="produce produce-three">🍅</div>
        </div>
    </div>
</section>
<section class="container section-space">
    <div class="section-title">
        <div><span class="eyebrow">Fresh this week</span>
            <h2>Popular products</h2>
        </div><a class="text-btn" href="shop.php">See all products →</a>
    </div>
    <div class="product-grid"><?php while ($p = mysqli_fetch_assoc($featured)): ?><article class="product-card"><a href="product.php?id=<?=$p['id']?>" class="product-image"><?=$p['emoji']?></a>
            <div class="product-body"><small><?=e($p['category'])?></small>
                <h3><a href="product.php?id=<?=$p['id']?>"><?=e($p['name'])?></a></h3>
                <p class="farmer-name">by <?=e($p['farmer'])?></p>
                <div class="product-bottom"><strong>৳<?=number_format($p['price'], 0)?><em>/<?=e($p['unit'])?></em></strong><a class="add-btn" href="cart.php?add=<?=$p['id']?>">Add +</a></div>
            </div>
        </article><?php endwhile;?></div>
</section>
<section class="how-section">
    <div class="container">
        <div class="section-title centered">
            <div><span class="eyebrow">How AgroDrop works</span>
                <h2>Good food in three easy steps</h2>
            </div>
        </div>
        <div class="steps">
            <div><b>1</b><span>🔎</span>
                <h3>Discover products</h3>
                <p>Browse fresh products from local farms.</p>
            </div>
            <div><b>2</b><span>🛒</span>
                <h3>Add to cart</h3>
                <p>Choose the items and quantities you need.</p>
            </div>
            <div><b>3</b><span>🏠</span>
                <h3>Get delivery</h3>
                <p>Checkout and receive your order at home.</p>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
