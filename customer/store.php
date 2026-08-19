<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


// ==================================================
// DATABASE
// ==================================================

require_once '../config/database.php';


// ==================================================
// ONLY POST REQUEST
// ==================================================

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
) {

    header(
        "Location: index.php"
    );

    exit;

}


// ==================================================
// GET FORM DATA
// ==================================================

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


// ==================================================
// VALIDATION
// ==================================================

if ($name === '') {

    header(
        "Location: index.php?error="
        . urlencode(
            'Customer name is required'
        )
    );

    exit;

}


if ($phone === '') {

    header(
        "Location: index.php?error="
        . urlencode(
            'Phone number is required'
        )
    );

    exit;

}


// ==================================================
// EMAIL VALIDATION
// ==================================================

if (
    $email !== '' &&
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    header(
        "Location: index.php?error="
        . urlencode(
            'Invalid email address'
        )
    );

    exit;

}


// ==================================================
// STATUS
// ==================================================

if (
    $status !== 0 &&
    $status !== 1
) {

    $status = 1;

}


// ==================================================
// INSERT DATA
// ==================================================

try {


    $sql = "
        INSERT INTO customers
        (
            name,
            phone,
            email,
            address,
            status
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ";


    // ==================================================
    // PREPARE
    // ==================================================

    $stmt = $conn->prepare(
        $sql
    );


    if (!$stmt) {

        throw new Exception(
            'Prepare failed: '
            . $conn->error
        );

    }


    // ==================================================
    // BIND
    // ==================================================

    $stmt->bind_param(
        "ssssi",
        $name,
        $phone,
        $email,
        $address,
        $status
    );


    // ==================================================
    // EXECUTE
    // ==================================================

    if (!$stmt->execute()) {

        throw new Exception(
            'Insert failed: '
            . $stmt->error
        );

    }


    // ==================================================
    // CLOSE
    // ==================================================

    $stmt->close();


    // ==================================================
    // REDIRECT TO INDEX
    // ==================================================

    header(
        "Location: index.php?success="
        . urlencode(
            'Customer added successfully'
        )
    );


    exit;


} catch (Exception $e) {


    // ==================================================
    // ERROR REDIRECT
    // ==================================================

    header(
        "Location: index.php?error="
        . urlencode(
            $e->getMessage()
        )
    );


    exit;

}
