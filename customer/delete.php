
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
| Only GET Request Allowed
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    header("Location: index.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| Get Customer ID
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


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
| Delete Customer
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | Check Customer Exists
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT id
        FROM customers
        WHERE id = ?
        LIMIT 1
    ");


    if (!$stmt) {

        throw new Exception(
            "Check customer prepare failed: " .
            $conn->error
        );

    }


    $stmt->bind_param(
        "i",
        $id
    );


    if (!$stmt->execute()) {

        throw new Exception(
            "Check customer failed: " .
            $stmt->error
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Do NOT use get_result()
    |--------------------------------------------------------------------------
    */

    $stmt->store_result();


    /*
    |--------------------------------------------------------------------------
    | Customer Not Found
    |--------------------------------------------------------------------------
    */

    if ($stmt->num_rows === 0) {

        $stmt->close();

        header(
            "Location: index.php?error=" .
            urlencode("Customer not found")
        );

        exit;

    }


    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Delete Customer
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        DELETE FROM customers
        WHERE id = ?
    ");


    if (!$stmt) {

        throw new Exception(
            "Delete prepare failed: " .
            $conn->error
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Bind ID
    |--------------------------------------------------------------------------
    */

    $stmt->bind_param(
        "i",
        $id
    );


    /*
    |--------------------------------------------------------------------------
    | Execute Delete
    |--------------------------------------------------------------------------
    */

    if (!$stmt->execute()) {

        throw new Exception(
            "Delete execute failed: " .
            $stmt->error
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Check Deleted
    |--------------------------------------------------------------------------
    */

    if ($stmt->affected_rows !== 1) {

        $stmt->close();

        header(
            "Location: index.php?error=" .
            urlencode("Customer was not deleted")
        );

        exit;

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
        urlencode("Customer deleted successfully")
    );

    exit;


} catch (Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | Show Actual Error
    |--------------------------------------------------------------------------
    */

    echo "<div style='
        font-family: Arial;
        padding: 30px;
        margin: 30px;
        background: #fff0f0;
        border: 1px solid #ff4444;
        border-radius: 8px;
    '>";

    echo "<h2 style='color:#d00;'>
        Delete Customer Failed
    </h2>";

    echo "<p>";
    echo htmlspecialchars(
        $e->getMessage(),
        ENT_QUOTES,
        'UTF-8'
    );
    echo "</p>";

    echo "<p>
        <strong>File:</strong>
        " . htmlspecialchars(
            $e->getFile(),
            ENT_QUOTES,
            'UTF-8'
        ) . "
    </p>";

    echo "<p>
        <strong>Line:</strong>
        " . (int) $e->getLine() . "
    </p>";

    echo "</div>";

    exit;

}