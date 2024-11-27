<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name']) || !isset($_SESSION['role']) || !isset($_SESSION['branch'])) {
    header("location: ../all_login.php");
    exit; // It's a good practice to exit after sending a header redirect
}

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
$branch = isset($_SESSION['branch_name']) ? $_SESSION['branch_name'] : null;

if (isset($_POST['void'])) {
    $margin = 0; // Initialize margin
    $thevalueofreturn = 0; // Initialize return value
    $order_details_idpid = $_POST['order_details_id'];
    $product_id = $_POST['product_id'];
    $order_id = $_POST['order_id'];
    $flavor_cost = $_POST['flavor_cost'];
    $total_amount = $_POST['total_amount']; // Corrected variable name
    $input_quantity = $_POST['input_quantity'];
    $current_quantity = $_POST['current_quantity'];
    $product_price = $_POST['product_price'];
    $product_name = $_POST['product_name'];
    
    $margin = floatval($flavor_cost) * intval($input_quantity); // Calculate margin
    $thevalueofreturn = intval($input_quantity) * floatval($product_price); // Calculate return value
    

    // Check if input quantity is greater than current quantity
    if ($current_quantity < $input_quantity) {
        $_SESSION['void'] = "Cannot void more than current quantity.";
        header("location: adminvoid.php?order_details_id=" . urlencode($order_details_idpid) . "&quantity=" . urlencode($input_quantity));
        exit(); // Exit after redirect
    } else {
        // Update the flavor quantity
        $sql = "UPDATE flavor SET quantity = quantity + ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $input_quantity, $product_id);
        mysqli_stmt_execute($stmt);

        // Update the total amount in orders
        $sql = "UPDATE orders SET TOTAL_AMT = TOTAL_AMT - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "di", $thevalueofreturn, $order_id); // Changed to "di" if TOTAL_AMT is decimal
        mysqli_stmt_execute($stmt);

        // Update the quantity in order_details
        $sql = "UPDATE order_details SET QTY = QTY - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $input_quantity, $order_details_idpid);
        mysqli_stmt_execute($stmt);

        // Update the total in order_details
        $sql = "UPDATE order_details SET TOTAL = TOTAL - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "di", $thevalueofreturn, $order_details_idpid); // Changed to "di" if TOTAL is decimal
        mysqli_stmt_execute($stmt);

        // Update the margin in order_details
        $sql = "UPDATE order_details SET MARGIN = MARGIN - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "di", $margin, $order_details_idpid); // Changed to "di" if MARGIN is decimal
        mysqli_stmt_execute($stmt);

        // Insert the returned item
        $sql = "INSERT INTO returned_items (order_number, product, quantity, price,date, b_id) VALUES (?, ?, ?,?, NOW(), ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isiii", $order_details_idpid, $product_name, $input_quantity,   $product_price, $branchid); // Changed order_details_idpid to order_id
        mysqli_stmt_execute($stmt);

        $_SESSION['voidsuccess'] = "Item voided successfully.";
        header("location: adminvoid.php");
        exit(); // Exit after redirect
    }
}

if (isset($_POST['dispose'])) {
    $margin = 0;
    $thevalueofreturn = 0;
    
    // Assign post data to more descriptive variables
    $orderDetailsId = $_POST['order_details_id'];
    $productId = $_POST['product_id'];
    $orderId = $_POST['order_id'];
    $flavorCost = $_POST['flavor_cost'];
    $totalAmount = $_POST['total_amount'];
    $inputQuantity = $_POST['input_quantity'];
    $currentQuantity = $_POST['current_quantity'];
    $productPrice = $_POST['product_price'];
    $productName = $_POST['product_name'];
    $reason = $_POST['reason'];
   

    // Calculate margin and value of return
    $margin = floatval($flavorCost) * intval($inputQuantity); // Ensure both are numeric
    $thevalueofreturn = intval($inputQuantity) * floatval($productPrice); // Ensure both are numeric
    

    // Check if current quantity is sufficient for disposal
    if ($currentQuantity < $inputQuantity) {
        $_SESSION['dispose'] = "Cannot process: insufficient quantity.";
        header("Location: adminvoid.php?order_details_id=" . urlencode($orderDetailsId) . "&quantity=" . urlencode($inputQuantity));
        exit();
    } else {
        // Update the total amount in the orders table
        $sql = "UPDATE orders SET TOTAL_AMT = TOTAL_AMT - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $thevalueofreturn, $orderId);
        mysqli_stmt_execute($stmt);

        // Update the quantity in the order_details table
        $sql = "UPDATE order_details SET QTY = QTY - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $inputQuantity, $orderDetailsId);
        mysqli_stmt_execute($stmt);

        // Update the total in the order_details table
        $sql = "UPDATE order_details SET TOTAL = TOTAL - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $thevalueofreturn, $orderDetailsId);
        mysqli_stmt_execute($stmt);

        // Update the margin in the order_details table
        $sql = "UPDATE order_details SET MARGIN = MARGIN - ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $margin, $orderDetailsId);
        mysqli_stmt_execute($stmt);

        // Insert the disposal record
        $sql = "INSERT INTO dispose (order_number, product, quantity, price, reason, date, b_id) 
                VALUES (?, ?, ?, ?, ?, NOW(), ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isiisi", $orderDetailsId, $productName, $inputQuantity, $productPrice, $reason, $branchid);
        mysqli_stmt_execute($stmt);

        // Set success message and redirect
        $_SESSION['voidsuccess'] = "Successfully Disposed.";
        header("Location: adminvoid.php");
        exit();
    }
}




?>