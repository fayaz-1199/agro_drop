<?php

$pageTitle = 'Customer Management';

require_once '../config/database.php';
require_once '../includes/header.php';


/*
|--------------------------------------------------------------------------
| Fetch Customers
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            id,
            name,
            phone,
            email,
            address,
            status,
            created_at
        FROM customers
        ORDER BY id DESC
    ");

    $customers = $stmt->fetchAll();

} catch (PDOException $e) {

    $customers = [];

    $databaseError = $e->getMessage();
}


/*
|--------------------------------------------------------------------------
| Edit Customer
|--------------------------------------------------------------------------
*/

$editCustomer = null;

if (isset($_GET['edit'])) {

    $editId = (int) $_GET['edit'];

    if ($editId > 0) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM customers
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $editId
        ]);

        $editCustomer = $stmt->fetch();
    }
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
         DATABASE ERROR
    ================================================== -->

    <?php if (isset($databaseError)): ?>

    <div class="alert alert-error">

        <span class="alert-icon">
            !
        </span>

        <span>
            Database Error:
            <?= htmlspecialchars(
                    $databaseError
                ) ?>
        </span>

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


            <button type="button" class="secondary-btn" id="openCustomerModal2">
                + Add Customer
            </button>

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


                    <?php foreach (
                            $customers as $customer
                        ): ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <span class="customer-id">

                                #
                                <?= (int)
                                            $customer['id']
                            ?>

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


                                <!-- VIEW -->

                                <a href="show.php?id=<?= (int) $customer['id'] ?>" class="action-btn view" title="View Customer">
                                    👁
                                </a>



                                <!-- EDIT -->

                                <a href="edit.php?id=<?= (int) $customer['id'] ?>" class="action-btn edit" title="Edit Customer">
                                    ✏
                                </a>



                                <!-- DELETE -->

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


                                <button type="button" class="primary-btn" id="openCustomerModal3">
                                    + Add Customer
                                </button>

                            </div>

                        </td>

                    </tr>


                    <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>

</div>



<!-- ==================================================
     ADD CUSTOMER MODAL
================================================== -->

<div id="customerModal" class="customer-modal">


    <!-- OVERLAY -->

    <div class="customer-modal-overlay"></div>



    <!-- MODAL -->

    <div class="customer-modal-content">


        <!-- MODAL HEADER -->

        <div class="customer-modal-header">

            <div class="form-title">

                <div class="form-title-icon">
                    👤
                </div>

                <div>

                    <h2>
                        Add Customer
                    </h2>

                    <p>
                        Enter new customer information.
                    </p>

                </div>

            </div>


            <button type="button" class="close-btn modal-close">
                ×
            </button>

        </div>



        <!-- FORM -->

        <form action="store.php" method="POST">


            <div class="modal-form-body">

                <div class="form-grid">


                    <!-- NAME -->

                    <div class="form-group">

                        <label>
                            Customer Name
                            <span>*</span>
                        </label>

                        <input type="text" name="name" placeholder="Enter customer name" required>

                    </div>



                    <!-- PHONE -->

                    <div class="form-group">

                        <label>
                            Phone Number
                            <span>*</span>
                        </label>

                        <input type="text" name="phone" placeholder="Enter phone number" required>

                    </div>



                    <!-- EMAIL -->

                    <div class="form-group">

                        <label>
                            Email Address
                        </label>

                        <input type="email" name="email" placeholder="Enter email address">

                    </div>



                    <!-- STATUS -->

                    <div class="form-group">

                        <label>
                            Status
                        </label>

                        <select name="status">

                            <option value="1">
                                Active
                            </option>

                            <option value="0">
                                Inactive
                            </option>

                        </select>

                    </div>



                    <!-- ADDRESS -->

                    <div class="form-group form-full">

                        <label>
                            Address
                        </label>

                        <textarea name="address" rows="4" placeholder="Enter customer address"></textarea>

                    </div>


                </div>

            </div>



            <!-- MODAL FOOTER -->

            <div class="customer-modal-footer">

                <button type="button" class="secondary-btn modal-close">
                    Cancel
                </button>


                <button type="submit" class="primary-btn">
                    Save Customer
                </button>

            </div>


        </form>

    </div>

</div>



<!-- ==================================================
     JAVASCRIPT
================================================== -->

<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const modal =
                document.getElementById(
                    'customerModal'
                );


            const openButtons = [

                document.getElementById(
                    'openCustomerModal'
                ),

                document.getElementById(
                    'openCustomerModal2'
                ),

                document.getElementById(
                    'openCustomerModal3'
                )

            ];


            const closeButtons =
                document.querySelectorAll(
                    '.modal-close'
                );


            const overlay =
                document.querySelector(
                    '.customer-modal-overlay'
                );


            /*
            |--------------------------------------------------------------------------
            | Open Modal
            |--------------------------------------------------------------------------
            */

            function openModal() {

                if (!modal) {
                    return;
                }

                modal.classList.add('show');

                document.body.classList.add(
                    'modal-open'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Close Modal
            |--------------------------------------------------------------------------
            */

            function closeModal() {

                if (!modal) {
                    return;
                }

                modal.classList.remove(
                    'show'
                );

                document.body.classList.remove(
                    'modal-open'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Open Buttons
            |--------------------------------------------------------------------------
            */

            openButtons.forEach(
                function(button) {

                    if (button) {

                        button.addEventListener(
                            'click',
                            openModal
                        );

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Close Buttons
            |--------------------------------------------------------------------------
            */

            closeButtons.forEach(
                function(button) {

                    button.addEventListener(
                        'click',
                        closeModal
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Overlay Click
            |--------------------------------------------------------------------------
            */

            if (overlay) {

                overlay.addEventListener(
                    'click',
                    closeModal
                );

            }


            /*
            |--------------------------------------------------------------------------
            | ESC Key
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modal &&
                        modal.classList.contains(
                            'show'
                        )
                    ) {

                        closeModal();

                    }

                }
            );

        }
    );

</script>


<?php

require_once '../includes/footer.php';

?>
