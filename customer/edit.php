<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Edit Customer';

require_once '../config/database.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php?error=" . urlencode("Invalid customer ID"));
    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            phone,
            email,
            address,
            status
        FROM customers
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        header(
            "Location: index.php?error=" .
            urlencode("Customer not found")
        );
        exit;
    }

} catch (PDOException $e) {

    die(
        "Database Error: " .
        htmlspecialchars($e->getMessage())
    );
}

require_once '../includes/header.php';

?>

<div class="page-container">

    <div class="page-header">

        <div class="page-header-left">

            <div class="page-icon">
                ✏️
            </div>

            <div>

                <h1>
                    Edit Customer
                </h1>

                <p>
                    Update customer information.
                </p>

            </div>

        </div>

        <a
            href="index.php"
            class="secondary-btn"
        >
            ← Back
        </a>

    </div>


    <div class="form-card">

        <div class="form-card-header">

            <div class="form-title">

                <div class="form-title-icon">
                    ✏️
                </div>

                <div>

                    <h2>
                        Update Customer
                    </h2>

                    <p>
                        Update customer information below.
                    </p>

                </div>

            </div>

        </div>


        <form
            action="update.php"
            method="POST"
        >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $customer['id'] ?>"
            >


            <div class="form-grid">


                <!-- NAME -->

                <div class="form-group">

                    <label>
                        Customer Name
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        placeholder="Enter customer name"
                        value="<?= htmlspecialchars(
                            $customer['name'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <!-- PHONE -->

                <div class="form-group">

                    <label>
                        Phone Number
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="phone"
                        placeholder="Enter phone number"
                        value="<?= htmlspecialchars(
                            $customer['phone'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        Email Address
                    </label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter email address"
                        value="<?= htmlspecialchars(
                            $customer['email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                    >

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        Status
                    </label>

                    <select name="status">

                        <option
                            value="1"
                            <?= ((int) $customer['status'] === 1)
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Active
                        </option>

                        <option
                            value="0"
                            <?= ((int) $customer['status'] === 0)
                                ? 'selected'
                                : ''
                            ?>
                        >
                            Inactive
                        </option>

                    </select>

                </div>


                <!-- ADDRESS -->

                <div class="form-group form-full">

                    <label>
                        Address
                    </label>

                    <textarea
                        name="address"
                        rows="4"
                        placeholder="Enter customer address"
                    ><?= htmlspecialchars(
                        $customer['address'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?></textarea>

                </div>


            </div>


            <div class="form-actions">

                <a
                    href="index.php"
                    class="secondary-btn"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="primary-btn"
                >
                    Update Customer
                </button>

            </div>

        </form>

    </div>

</div>

<?php

require_once '../includes/footer.php';

?>