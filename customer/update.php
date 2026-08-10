<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

require_once '../config/database.php';


/*
|--------------------------------------------------------------------------
| Only POST Request Allowed
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| Get Form Data
|--------------------------------------------------------------------------
*/

$id = isset($_POST['id'])
    ? (int) $_POST['id']
    : 0;

$name = trim(
    $_POST['name'] ?? ''
);

$phone = trim(
    $_POST['phone'] ?? ''
);

$email = trim(
    $_POST['email'] ?? ''
);

$address = trim(
    $_POST['address'] ?? ''
);

$status = isset($_POST['status'])
    ? (int) $_POST['status']
    : 1;


/*
|--------------------------------------------------------------------------
| Validate Customer ID
|--------------------------------------------------------------------------
*/

if ($id <= 0) {

    header(
        "Location: index.php?error=" .
        urlencode("Invalid customer ID")
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Validate Name
|--------------------------------------------------------------------------
*/

if ($name === '') {

    header(
        "Location: edit.php?id=" .
        $id .
        "&error=" .
        urlencode("Customer name is required")
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Validate Phone
|--------------------------------------------------------------------------
*/

if ($phone === '') {

    header(
        "Location: edit.php?id=" .
        $id .
        "&error=" .
        urlencode("Phone number is required")
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Validate Email
|--------------------------------------------------------------------------
*/

if (
    $email !== '' &&
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    header(
        "Location: edit.php?id=" .
        $id .
        "&error=" .
        urlencode("Invalid email address")
    );

    exit;

}


/*
|--------------------------------------------------------------------------
| Validate Status
|--------------------------------------------------------------------------
*/

if ($status !== 0 && $status !== 1) {

    $status = 1;

}


/*
|--------------------------------------------------------------------------
| Update Customer
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | Check Customer Exists
    |--------------------------------------------------------------------------
    */

    $checkStmt = $conn->prepare("
        SELECT id
        FROM customers
        WHERE id = ?
        LIMIT 1
    ");


    if (!$checkStmt) {

        throw new Exception(
            "Prepare failed: " .
            $conn->error
        );

    }


    $checkStmt->bind_param(
        "i",
        $id
    );


    $checkStmt->execute();


    $result = $checkStmt->get_result();


    $customer = $result->fetch_assoc();


    $checkStmt->close();


    /*
    |--------------------------------------------------------------------------
    | Customer Not Found
    |--------------------------------------------------------------------------
    */

    if (!$customer) {

        header(
            "Location: index.php?error=" .
            urlencode("Customer not found")
        );

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | Prepare Update Query
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        UPDATE customers
        SET
            name = ?,
            phone = ?,
            email = ?,
            address = ?,
            status = ?
        WHERE id = ?
    ");


    if (!$stmt) {

        throw new Exception(
            "Update prepare failed: " .
            $conn->error
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Convert Empty Values to NULL
    |--------------------------------------------------------------------------
    */

    $emailValue = $email !== ''
        ? $email
        : null;


    $addressValue = $address !== ''
        ? $address
        : null;


    /*
    |--------------------------------------------------------------------------
    | Bind Parameters
    |--------------------------------------------------------------------------
    */

    $stmt->bind_param(
        "ssssii",
        $name,
        $phone,
        $emailValue,
        $addressValue,
        $status,
        $id
    );


    /*
    |--------------------------------------------------------------------------
    | Execute Update
    |--------------------------------------------------------------------------
    */

    if (!$stmt->execute()) {

        throw new Exception(
            "Update failed: " .
            $stmt->error
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Close Statement
    |--------------------------------------------------------------------------
    */

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Redirect to Customer Index
    |--------------------------------------------------------------------------
    */

    header(
        "Location: index.php?success=" .
        urlencode(
            "Customer updated successfully"
        )
    );

    exit;


} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | Show Error During Development
    |--------------------------------------------------------------------------
    */

    die(
        "<h3>Update Customer Failed</h3>" .
        "<p>" .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        ) .
        "</p>"
    );

}