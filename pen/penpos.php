<?php 
session_start();
require '../database/db_conn.php';
if (isset($_SESSION['usesr_id']) && isset($_SESSION['user_name'])) {
    // You might want to put some code here related to logged-in user behavior
}


$userid = $_SESSION['user_id'];
$branchid = $_SESSION['branch'];
$branchname = $_SESSION['branchname'];
$username = $_SESSION['user_name'];
if (isset($_POST["addcartnaten"])) {
    // Sanitize input data to prevent SQL injection
    $userid = $_SESSION['user_id'];
    $branchid = $_SESSION['branch'];
    $currentPage = $_POST['currentPage'];
    // Check Start of the Day status
    $sql = "SELECT sod FROM users WHERE id = ? AND b_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $userid, $branchid);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $sod);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($sod <= 0) {
        $_SESSION['poserrors'] = "Insert Start of the Day First";
        header("location: pen_pos.php");
        exit;
    } else {
        $item = [
            "id" => intval($_POST["id"]),
            "category" => htmlspecialchars($_POST["category"]),
            "flavor" => htmlspecialchars($_POST["flavor"]),
            "price" => floatval($_POST["price"]),
            "qty" => intval($_POST["qty"]),
            "cost" => floatval($_POST["cost"]) * intval($_POST["qty"]),
            "total" => floatval($_POST["price"]) * intval($_POST["qty"]),
        ];

        // Check if there's sufficient stock
        $sql = "SELECT quantity FROM flavor WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $item['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $qty);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($item['qty'] > $qty) {
            $_SESSION['poserror'] = "Insufficient Stock";
            header("location: pen_pos.php");
            exit;
        }

        // Check if item is already in cart
        $zzero = 1;
        $sql = "SELECT qty, total FROM cart WHERE userid = ? AND itemid = ? AND b_id  = ? AND tiny =  ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", $userid, $item['id'], $branchid, $zzero);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $existingQty, $existingTotal);
        $itemInCart = mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($itemInCart) {
            // Update the quantity and total if item is already in the cart
            $newQty = $existingQty + $item['qty'];
            $newTotal = $existingTotal + $item['total'];

            // Validate the new quantity against flavor stock
            if ($newQty > $qty) {
                $_SESSION['poserror'] = "Not enough stocks: " . mysqli_error($conn);
                header("location: pen_pos.php");
                exit;
            }

            $sql = "UPDATE cart SET qty = ?, total = ?, cost = ? WHERE userid = ? AND itemid = ? AND b_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iidiii", $newQty, $newTotal, $item['cost'], $userid, $item['id'], $branchid);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $_SESSION['possucc'] = "Adding Quantity Successfully";
                header("location: pen_pos.php");
                exit;
            } else {
                $_SESSION['poserror'] = "Error updating cart: " . mysqli_error($conn);
                header("location: pen_pos.php");
                exit;
            }
        } else {
            $tiny = 1;
            $sql = "INSERT INTO cart (userid, itemid, category_category, price, qty, total, cost, tiny, b_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iisiiiiii", $userid, $item['id'], $item['category'], $item['price'], $item['qty'], $item['total'], $item['cost'], $tiny, $branchid);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $_SESSION['possucc'] = "Added to Cart Successfully";
                header("Location: pen_pos.php?page=" . $currentPage);
                exit;
            } else {
                $_SESSION['poserror'] = "Error inserting into cart: " . mysqli_error($conn);
                header("location: pen_pos.php");
                exit;
            }
        }
    }
}
