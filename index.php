<?php

$pageTitle = 'Home';

require_once 'includes/header.php';

?>

<section class="hero">

    <div class="container hero-content">

        <div class="hero-text">

            <span class="hero-badge">
                🌾 Fresh & Quality Agricultural Products
            </span>

            <h1>
                Fresh Products
                <br>
                <span>Directly From Agriculture</span>
            </h1>

            <p>
                Agro Drop connects customers with
                quality agricultural products at
                a fair price.
            </p>

            <div class="hero-buttons">

                <a
                    href="product/index.php"
                    class="primary-btn"
                >
                    Shop Products →
                </a>

                <a
                    href="customer/create.php"
                    class="secondary-btn"
                >
                    Add Customer
                </a>

            </div>

        </div>


        <div class="hero-image">

            <div class="hero-image-box">

                🌾

                <div class="floating-card card-one">
                    🌱 Fresh Products
                </div>

                <div class="floating-card card-two">
                    ⭐ Trusted Quality
                </div>

            </div>

        </div>

    </div>

</section>


<section class="features">

    <div class="container features-grid">

        <div class="feature-card">

            <div class="feature-icon">
                👨‍🌾
            </div>

            <div>

                <h3>
                    Customers
                </h3>

                <p>
                    Manage Agro Drop customers.
                </p>

            </div>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                🌾
            </div>

            <div>

                <h3>
                    Products
                </h3>

                <p>
                    Manage agricultural products.
                </p>

            </div>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                🛒
            </div>

            <div>

                <h3>
                    Orders
                </h3>

                <p>
                    Manage customer orders.
                </p>

            </div>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                ⭐
            </div>

            <div>

                <h3>
                    Reviews
                </h3>

                <p>
                    Manage customer reviews.
                </p>

            </div>

        </div>

    </div>

</section>


<section class="category-section">

    <div class="container">

        <div class="section-heading">

            <span>
                Agro Drop
            </span>

            <h2>
                Agricultural Marketplace
            </h2>

            <p>
                Manage customers, products,
                orders and reviews.
            </p>

        </div>


        <div class="category-grid">

            <a
                href="customer/index.php"
                class="category-card"
            >

                <div class="category-icon">
                    👨‍🌾
                </div>

                <h3>
                    Customer
                </h3>

                <p>
                    Add, update, show and delete customers.
                </p>

            </a>


            <a
                href="product/index.php"
                class="category-card"
            >

                <div class="category-icon">
                    🌾
                </div>

                <h3>
                    Product
                </h3>

                <p>
                    Manage agricultural products.
                </p>

            </a>


            <a
                href="order/index.php"
                class="category-card"
            >

                <div class="category-icon">
                    🛒
                </div>

                <h3>
                    Order
                </h3>

                <p>
                    Manage customer orders.
                </p>

            </a>


            <a
                href="review/index.php"
                class="category-card"
            >

                <div class="category-icon">
                    ⭐
                </div>

                <h3>
                    Review
                </h3>

                <p>
                    Manage product reviews.
                </p>

            </a>

        </div>

    </div>

</section>


<?php

require_once 'includes/footer.php';

?>
