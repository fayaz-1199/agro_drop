<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Customer Management';

require_once '../config/database.php';
require_once '../includes/header.php';


// ==================================================
// EDIT CUSTOMER
// ==================================================

$editCustomer = null;

if (isset($_GET['edit'])) {

    $editId = (int) $_GET['edit'];

    if ($editId > 0) {

        $stmt = $conn->prepare("
            SELECT *
            FROM customers
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            "i",
            $editId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $editCustomer = $result->fetch_assoc();

        $stmt->close();

    }

}


// ==================================================
// GET ALL CUSTOMERS
// ==================================================

$result = $conn->query("
    SELECT *
    FROM customers
    ORDER BY id DESC
");


if (!$result) {

    die(
        "Customer Query Failed: "
        . $conn->error
    );

}


$customers = [];

while ($row = $result->fetch_assoc()) {

    $customers[] = $row;

}

?>


<div class="customer-page">

    <!-- ==================================================
         SUCCESS MESSAGE
    ================================================== -->

    <?php if (isset($_GET['success'])): ?>

    <div class="alert alert-success">

        <span class="alert-icon">
            ✓
        </span>

        <span>
            <?= htmlspecialchars(
                    $_GET['success']
                ) ?>
        </span>

    </div>

    <?php endif; ?>


    <!-- ==================================================
         ERROR MESSAGE
    ================================================== -->

    <?php if (isset($_GET['error'])): ?>

    <div class="alert alert-error">

        <span class="alert-icon">
            !
        </span>

        <span>
            <?= htmlspecialchars(
                    $_GET['error']
                ) ?>
        </span>

    </div>

    <?php endif; ?>


    <!-- ==================================================
         ADD / EDIT FORM
    ================================================== -->

    <?php if (isset($_GET['add']) || $editCustomer): ?>

    <div class="form-card">


        <div class="form-card-header">

            <div class="form-title">

                <div class="form-title-icon">

                    <?= $editCustomer
                            ? '✏️'
                            : '👤'
        ?>

                </div>

                <div>

                    <h2>

                        <?= $editCustomer
                ? 'Edit Customer'
                : 'Add Customer'
        ?>

                    </h2>

                    <p>

                        <?= $editCustomer
            ? 'Update customer information.'
            : 'Enter new customer information.'
        ?>

                    </p>

                </div>

            </div>


            <a href="index.php" class="close-btn">
                ×
            </a>

        </div>


        <!-- FORM -->

        <form action="<?= $editCustomer
                    ? 'update.php'
                    : 'store.php'
        ?>" method="POST">


            <?php if ($editCustomer): ?>

            <input type="hidden" name="id" value="<?= $editCustomer['id'] ?>">

            <?php endif; ?>


            <div class="form-grid">


                <!-- NAME -->

                <div class="form-group">

                    <label>
                        Customer Name
                        <span>*</span>
                    </label>

                    <input type="text" name="name" placeholder="Enter customer name" value="<?= htmlspecialchars(
                                $editCustomer['name'] ?? ''
                            ) ?>" required>

                </div>


                <!-- PHONE -->

                <div class="form-group">

                    <label>
                        Phone Number
                        <span>*</span>
                    </label>

                    <input type="text" name="phone" placeholder="Enter phone number" value="<?= htmlspecialchars(
                                $editCustomer['phone'] ?? ''
                            ) ?>" required>

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        Email Address
                    </label>

                    <input type="email" name="email" placeholder="Enter email address" value="<?= htmlspecialchars(
                                $editCustomer['email'] ?? ''
                            ) ?>">

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label>
                        Status
                    </label>

                    <select name="status">

                        <option value="1" <?= (
                                    !$editCustomer ||
                                    $editCustomer['status'] == 1
                                )
                                    ? 'selected'
                                    : ''
        ?>>
                            Active
                        </option>

                        <option value="0" <?= (
                                    $editCustomer &&
                                    $editCustomer['status'] == 0
                                )
            ? 'selected'
            : ''
        ?>>
                            Inactive
                        </option>

                    </select>

                </div>


                <!-- ADDRESS -->

                <div class="form-group form-full">

                    <label>
                        Address
                    </label>

                    <textarea name="address" rows="4" placeholder="Enter customer address"><?= htmlspecialchars(
                            $editCustomer['address'] ?? ''
                        ) ?></textarea>

                </div>


            </div>


            <!-- FORM BUTTONS -->

            <div class="form-actions">

                <a href="index.php" class="secondary-btn">
                    Cancel
                </a>


                <button type="submit" class="primary-btn">

                    <?= $editCustomer
                            ? 'Update Customer'
                            : 'Save Customer'
        ?>

                </button>

            </div>


        </form>

    </div>

    <?php endif; ?>


    <!-- ==================================================
         CUSTOMER LIST
    ================================================== -->

    <div class="table-card">


        <!-- TABLE HEADER -->

        <div class="table-header">

            <div>

                <h2>
                    Customer List
                </h2>

                <p>
                    Total Customers:
                    <strong>
                        <?= count($customers) ?>
                    </strong>
                </p>

            </div>


            <a href="index.php?add=1" class="secondary-btn">
                + Add Customer
            </a>

        </div>


        <!-- TABLE -->

        <div class="table-responsive">

            <table class="customer-table">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Address
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php if (!empty($customers)): ?>


                    <?php foreach ($customers as $customer): ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <span class="customer-id">

                                #
                                <?= $customer['id'] ?>

                            </span>

                        </td>


                        <!-- NAME -->

                        <td>

                            <div class="customer-name">

                                <div class="customer-avatar">

                                    <?= strtoupper(
                                                substr(
                                                    $customer['name'],
                                                    0,
                                                    1
                                                )
                                            ) ?>

                                </div>


                                <div>

                                    <strong>

                                        <?= htmlspecialchars(
                                                    $customer['name']
                                                ) ?>

                                    </strong>

                                </div>

                            </div>

                        </td>


                        <!-- PHONE -->

                        <td>

                            <span class="phone-text">

                                📞

                                <?= htmlspecialchars(
                                            $customer['phone']
                                        ) ?>

                            </span>

                        </td>


                        <!-- EMAIL -->

                        <td>

                            <?php if (
                                        !empty(
                                            $customer['email']
                                        )
                                    ): ?>

                            <?= htmlspecialchars(
                                            $customer['email']
                                        ) ?>

                            <?php else: ?>

                            <span class="muted">
                                —
                            </span>

                            <?php endif; ?>

                        </td>


                        <!-- ADDRESS -->

                        <td>

                            <?php if (
                                        !empty(
                                            $customer['address']
                                        )
                                    ): ?>

                            <span class="address-text" title="<?= htmlspecialchars(
                                                $customer['address']
                                            ) ?>">

                                <?= htmlspecialchars(
                                                $customer['address']
                                            ) ?>

                            </span>

                            <?php else: ?>

                            <span class="muted">
                                —
                            </span>

                            <?php endif; ?>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <?php if (
                                        $customer['status'] == 1
                                    ): ?>

                            <span class="status active">

                                <span>
                                    ●
                                </span>

                                Active

                            </span>

                            <?php else: ?>

                            <span class="status inactive">

                                <span>
                                    ●
                                </span>

                                Inactive

                            </span>

                            <?php endif; ?>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <div class="action-buttons">


                                <a href="show.php?id=<?= $customer['id'] ?>" class="action-btn view" title="View Customer">
                                    👁
                                </a>


                                <a href="index.php?edit=<?= $customer['id'] ?>" class="action-btn edit" title="Edit Customer">
                                    ✏
                                </a>


                                <a href="delete.php?id=<?= (int) $customer['id'] ?>" class="action-btn delete" title="Delete Customer" onclick="return confirm('Are you sure you want to delete this customer?')">
                                    🗑
                                </a>


                            </div>

                        </td>


                    </tr>


                    <?php endforeach; ?>


                    <?php else: ?>


                    <tr>

                        <td colspan="7" class="empty-data">

                            <div class="empty-state">

                                <div class="empty-icon">
                                    👥
                                </div>

                                <h3>
                                    No Customers Found
                                </h3>

                                <p>
                                    Start by adding your first customer.
                                </p>

                                <a href="index.php?add=1" class="primary-btn">
                                    + Add Customer
                                </a>

                            </div>

                        </td>

                    </tr>


                    <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>


</div>


<?php

require_once '../includes/footer.php';

?>
