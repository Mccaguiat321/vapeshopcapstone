<?php
require '../database/db_conn.php';
if (isset($_SESSION['usesr_id']) && isset($_SESSION['user_name'])) {
    // You might want to put some code here related to logged-in user behavior
}
$userid = $_SESSION['user_id'];
$branchid = $_SESSION['branch'];
$branchname = $_SESSION['branchname'];
$username = $_SESSION['user_name'];
require '../vendor/autoload.php'; 

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;




$returntozero = 0;
$tinyyy = 1;
$mc = "caguiat";


date_default_timezone_set('Asia/Manila'); 
$targetTime = '11:59 PM';
$currentTime = date('g:i A', time()); 

if ($currentTime === $targetTime) {
    $sql = "UPDATE users SET status = 0 WHERE status = 1";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute(); 
        if ($stmt->affected_rows > 0) {
            // Rows were updated
        } else {
            // No rows were updated
        }
    } catch (mysqli_sql_exception $e) {
        // Error updating database
    }
} else {
    // Current time does not match target time
}




if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cashpayment"])) {
    $ordertype = "1";
    $subtotalInput = floatval($_POST["subtotalInput"]);
    $payment = floatval($_POST["cashpaymentinput"]);

    // Check if payment is sufficient
    if ($payment < $subtotalInput) {
        $_SESSION['poserrorsss'] = "Not enough balance";
        header("Location: pen_pos.php");
        exit;
    } else {
        $change = $payment - $subtotalInput;

        // Prepare and execute statement to update nsod
        $update_sod = "UPDATE users SET nsod = nsod + ? WHERE id = ? AND b_id = ?";
        $stmt = mysqli_prepare($conn, $update_sod);
        mysqli_stmt_bind_param($stmt, "iii", $subtotalInput, $userid, $branchid);
        if (!mysqli_stmt_execute($stmt)) {
            $_SESSION['poserr'] = "Error: Unable to update NSOD";
            header("Location: pen_pos.php");
            exit;
        } else {
            $_SESSION['possucc'] = "NSOD updated successfully";
        }

        // Insert customer details
        $sql = "INSERT INTO customer_details (NAME, b_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $mc, $branchid);
        if (!mysqli_stmt_execute($stmt)) {
            $_SESSION['poserror'] = "Error inserting customer details";
            header("location: pen_pos.php");
            exit;
        }

        

        $order_no = rand(10000, 100000);
        $_SESSION['order_number'] = $order_no;

        // Insert order
        $sql = "INSERT INTO orders (ORDER_NO, ORDER_DATE, UID, TOTAL_AMT, payment, b_id) VALUES (?, NOW(), ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiddi", $order_no, $userid, $subtotalInput, $payment, $branchid);
        if (!mysqli_stmt_execute($stmt)) {
            $_SESSION['poserror'] = "Error inserting order";
            header("location: pen_pos.php");
            exit;
        }
        $oid = $conn->insert_id;

        // Fetch cart items
        $sql_cart = "SELECT * FROM cart WHERE b_id = ? AND userid = ? AND tiny = 1";
        $stmt_cart = mysqli_prepare($conn, $sql_cart);
        mysqli_stmt_bind_param($stmt_cart, "ii", $branchid, $userid);
        mysqli_stmt_execute($stmt_cart);
        $result_cart = mysqli_stmt_get_result($stmt_cart);

        if ($result_cart->num_rows > 0) {
            while ($row_cart = $result_cart->fetch_assoc()) {
                // Insert order details
                $itemid = $row_cart['itemid'];
                $price = $row_cart['price'];
                $qty = $row_cart['qty'];
                $total = $row_cart['total'];
                $cost = $row_cart['cost'];
                $margin = $total - $cost;

                $sql_details = "INSERT INTO order_details (OID, PID, PRICE, QTY, TOTAL, MARGIN, ORDER_DATE, b_id, type) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?)";
                $stmt_details = mysqli_prepare($conn, $sql_details);
                mysqli_stmt_bind_param($stmt_details, "iiidiiii", $oid, $itemid, $price, $qty, $total, $margin, $branchid, $ordertype);
                if (!mysqli_stmt_execute($stmt_details)) {
                    $_SESSION['poserror'] = "Error inserting order detail";
                    header("location: pen_pos.php");
                    exit;
                }
            }


            $sql_update_flavor = "UPDATE flavor SET quantity = quantity - ? WHERE id = ?";
            $stmt_update_flavor = mysqli_prepare($conn, $sql_update_flavor);
            mysqli_stmt_bind_param($stmt_update_flavor, "ii", $qty, $itemid);
            
            if (!mysqli_stmt_execute($stmt_update_flavor)) {
                $_SESSION['poserror'] = "Error updating flavor quantity";
                header("location: pen_pos.php");
                exit;
            }

            // Fetch subtotal from cart
            $sql_subtotal = "SELECT SUM(total) AS subtotal FROM `cart` WHERE userid = ? AND b_id = ? AND tiny = ?";
            $stmt_subtotal = mysqli_prepare($conn, $sql_subtotal);
            mysqli_stmt_bind_param($stmt_subtotal, "iii", $staff_id, $branchid, $tinyyy);
            mysqli_stmt_execute($stmt_subtotal);
            $result_subtotal = mysqli_stmt_get_result($stmt_subtotal);

            if ($result_subtotal) {
                $row_subtotal = mysqli_fetch_assoc($result_subtotal);
                $subtotal = $row_subtotal["subtotal"];
            } else {
                $_SESSION['poserror'] = "Error fetching subtotal";
                header("location: pen_pos.php");
                exit;
            }

            // Fetch order details for printing receipt
            $sql_order_details = "SELECT users.user_name, flavor.flavor, order_details.PRICE, category.category, order_details.QTY, order_details.TOTAL 
                FROM order_details 
                INNER JOIN orders ON orders.id = order_details.OID 
                INNER JOIN users ON users.id = orders.UID  
                INNER JOIN flavor ON flavor.id = order_details.PID
                INNER JOIN category ON category.id = flavor.rs_id
                WHERE order_details.b_id = ? AND orders.UID = ? AND orders.ORDER_NO = ?";
                
            $stmt_order_details = mysqli_prepare($conn, $sql_order_details);
            mysqli_stmt_bind_param($stmt_order_details, "iii", $branchid, $userid, $order_no);
            mysqli_stmt_execute($stmt_order_details);
            $result_order_details = mysqli_stmt_get_result($stmt_order_details);

            if ($result_order_details->num_rows > 0) {
                $items = array(); // Initialize array to store items
                $total_amount = 0;

                while ($row = $result_order_details->fetch_assoc()) {
                    // Insert order details
                    $flavor = $row['flavor'];
                    $price = $row['PRICE'];
                    $qty = $row['QTY'];
                    $category = $row['category'];
                    $total = $row['TOTAL'];

                    // Calculate total amount
                    $total_amount += $total;

                    // Add item to the items array
                    $items[] = array('flavor' => $flavor, 'price' => $price, 'qty' => $qty, 'category' => $category);
                }
                
                // Initialize USB printer connection
                $connector = new WindowsPrintConnector("POS-X Thermal Printer"); // Replace with your printer's name
                $printer = new Printer($connector);

                // Print receipt content
                $printer->initialize(); // Initialize printer
                $printer->setJustification(Printer::JUSTIFY_CENTER); // Center-align text

                // Header 
                $printer->text("Receipt\n");
                $printer->text("Cloud Keepers Vape Lounge\n");
                $printer->text("Branch:    $branchname\n");
                $printer->text("Order No:   $order_no\n");
                $printer->text("--------------------------------\n");

                // Receipt body
                $printer->setJustification(Printer::JUSTIFY_LEFT); // Left-align text
                date_default_timezone_set('Asia/Manila');
                $printer->text("Date: " . date("Y-m-d H:i:s") . "\n");
                $printer->text(str_pad("Flavor", 12) . str_pad("Price", 10) . "Quantity\n");

                foreach ($items as $item) {
                    $flavor = str_pad($item['flavor'], 12);
                    $price = str_pad($item['price'], 10);
                    $quantity = $item['qty'];
                    $printer->text("$flavor $price $quantity\n");
                }
                
                $printer->text("--------------------------------\n");
                $printer->text("Total:                $subtotalInput\n");
                $printer->text("Cash:                 $payment\n");
                $printer->text("--------------------------------\n");
                $printer->text("Change:                $change\n");
                $printer->text("Staff:                $username \n");

                // Footer
                $printer->text("--------------------------------\n");
                $printer->text("Thank you for shopping with us!\n");
                $printer->text("This is Not An Official Receipt!\n");

                // Cut the receipt
                $printer->cut();

                // Close the printer connection
                $printer->close();
                
                // Update cart
                $update_cart = "UPDATE cart SET tiny = ? WHERE userid = ? AND b_id = ? AND tiny = 1";
                $stmt_update_cart = mysqli_prepare($conn, $update_cart);
                mysqli_stmt_bind_param($stmt_update_cart, "iii", $tinyy, $userid, $branchid);
                if (!mysqli_stmt_execute($stmt_update_cart)) {
                    $_SESSION['poserror'] = "Error updating cart";
                    header("location: pen_pos.php");
                    exit;
                }

                $_SESSION['possucc'] = "Checkout successful";
                header("location: pen_pos.php");
                exit;
            } else {
                $_SESSION['poserror'] = "No order details found";
                header("location: pen_pos.php");
                exit;
            }
        } else {
            $_SESSION['poserror'] = "No records found in cart";
            header("location: pen_pos.php");
            exit;
        }
    }
}



// if (isset($_POST["addcartnaten"])) {
//     // Sanitize input data to prevent SQL injection
//     $userid = $_SESSION['user_id'];
//     $branchid = $_SESSION['branch'];

//     // Check Start of the Day status
//     $sql = "SELECT sod FROM users WHERE id = ? AND b_id = ?";
//     $stmt = mysqli_prepare($conn, $sql);
//     mysqli_stmt_bind_param($stmt, "ii", $userid, $branchid);
//     mysqli_stmt_execute($stmt);
//     mysqli_stmt_bind_result($stmt, $sod);
//     mysqli_stmt_fetch($stmt);
//     mysqli_stmt_close($stmt);

//     if ($sod <= 0) {
//         $_SESSION['poserrors'] = "Insert Start of the Day First";
//         header("location: pen_pos.php");
//         exit;
//     } else {
//         $item = [
//             "id" => intval($_POST["id"]),
//             "category" => htmlspecialchars($_POST["category"]),
//             "flavor" => htmlspecialchars($_POST["flavor"]),
//             "price" => floatval($_POST["price"]),
//             "qty" => intval($_POST["qty"]),
//             "cost" => floatval($_POST["cost"]) * intval($_POST["qty"]),
//             "total" => floatval($_POST["price"]) * intval($_POST["qty"]),
//         ];

//         // Check if there's sufficient stock
//         $sql = "SELECT quantity FROM flavor WHERE id = ?";
//         $stmt = mysqli_prepare($conn, $sql);
//         mysqli_stmt_bind_param($stmt, "i", $item['id']);
//         mysqli_stmt_execute($stmt);
//         mysqli_stmt_bind_result($stmt, $qty);
//         mysqli_stmt_fetch($stmt);
//         mysqli_stmt_close($stmt);

//         if ($item['qty'] > $qty) {
//             $_SESSION['poserror'] = "Insufficient Stock";
//             header("location: pen_pos.php");
//             exit;
//         }

//         // Check if item is already in cart
//         $zzero = 1;
//         $sql = "SELECT qty, total FROM cart WHERE userid = ? AND itemid = ? AND b_id  = ? AND tiny =  ?";
//         $stmt = mysqli_prepare($conn, $sql);
//         mysqli_stmt_bind_param($stmt, "iiii", $userid, $item['id'], $branchid, $zzero);
//         mysqli_stmt_execute($stmt);
//         mysqli_stmt_bind_result($stmt, $existingQty, $existingTotal);
//         $itemInCart = mysqli_stmt_fetch($stmt);
//         mysqli_stmt_close($stmt);

//         if ($itemInCart) {
//             // Update the quantity and total if item is already in the cart
//             $newQty = $existingQty + $item['qty'];
//             $newTotal = $existingTotal + $item['total'];

//             // Validate the new quantity against flavor stock
//             if ($newQty > $qty) {
//                 $_SESSION['poserror'] = "Not enough stocks: " . mysqli_error($conn);
//                 header("location: pen_pos.php");
//                 exit;
//             }

//             $sql = "UPDATE cart SET qty = ?, total = ?, cost = ? WHERE userid = ? AND itemid = ? AND b_id = ?";
//             $stmt = mysqli_prepare($conn, $sql);
//             if ($stmt) {
//                 mysqli_stmt_bind_param($stmt, "iidiii", $newQty, $newTotal, $item['cost'], $userid, $item['id'], $branchid);
//                 mysqli_stmt_execute($stmt);
//                 mysqli_stmt_close($stmt);

//                 $_SESSION['possucc'] = "Cart updated successfully";
//                 header("location: pen_pos.php");
//                 exit;
//             } else {
//                 $_SESSION['poserror'] = "Error updating cart: " . mysqli_error($conn);
//                 header("location: pen_pos.php");
//                 exit;
//             }
//         } else {
//             $tiny = 1;
//             $sql = "INSERT INTO cart (userid, itemid, category_category, price, qty, total, cost, tiny, b_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
//             $stmt = mysqli_prepare($conn, $sql);
//             if ($stmt) {
//                 mysqli_stmt_bind_param($stmt, "iisiiiiii", $userid, $item['id'], $item['category'], $item['price'], $item['qty'], $item['total'], $item['cost'], $tiny, $branchid);
//                 mysqli_stmt_execute($stmt);
//                 mysqli_stmt_close($stmt);

//                 $_SESSION['possucc'] = "Added to Cart Successfully";
//                 header("location: pen_pos.php");
//                 exit;
//             } else {
//                 $_SESSION['poserror'] = "Error inserting into cart: " . mysqli_error($conn);
//                 header("location: pen_pos.php");
//                 exit;
//             }
//         }
//     }
// }


if (isset($_POST["sod"])) {
    $startofday = $_POST['startofday'];

    // Update sod column
    $sql_sod = "UPDATE `users` SET `sod` = ?, `nsod` = ? WHERE `id` = ? AND `b_id` = ?";
    $stmt_sod = mysqli_prepare($conn, $sql_sod);
    mysqli_stmt_bind_param($stmt_sod, "iiii", $startofday, $startofday, $userid, $branchid);
    $success_sod = mysqli_stmt_execute($stmt_sod);


    // Check if both updates were successful
    if ($success_sod ) {
        $_SESSION['possucc'] = "Start of day successfully inserted ";
    } else {
        $_SESSION['poserr'] = "Error: Unable to update start of day for sod or nsod";
    }

    // Close statements and redirect back to the page
    mysqli_stmt_close($stmt_sod);

    header("Location: pen_pos.php");
    exit();
}
if (isset($_POST["eod"])) {
    $endofday = $_POST['endofday'];
    $returntozero = 0;
    $typeoftype = 1;

    // Prepare and execute query to get nsod
    $sql = "SELECT nsod FROM users WHERE id = ? AND b_id = ?";
    $stmt_sod = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt_sod, "ii", $userid, $branchid); 
    mysqli_stmt_execute($stmt_sod);
    $result_sod = mysqli_stmt_get_result($stmt_sod);

    if ($result_sod && $row_sod = mysqli_fetch_assoc($result_sod)) {
        $end_of_day_value = $row_sod['nsod'];
        $total = $end_of_day_value - $endofday; // Subtracting end of day value from nsod
        if ($total > 0) {
            $text = "Insufficient funds";
        } elseif ($total < 0) {
            $text = "Excess funds";
        } else {
            $text = "Funds are balanced";
        }
        $sql_insert = "INSERT INTO daily_tally(stafftally, systemtallly, result_tally, result, date, user_id, b_id) VALUES (?, ?, ?, ?, NOW(), ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iissii", $endofday, $end_of_day_value, $text, $total, $userid, $branchid); 
        mysqli_stmt_execute($stmt_insert);

        // Reset sod and nsod to zero
        $sql_reset = "UPDATE users SET sod = ?, nsod = ? WHERE id = ? AND b_id = ?";
        $stmt_reset = mysqli_prepare($conn, $sql_reset);
        mysqli_stmt_bind_param($stmt_reset, "iiii", $returntozero, $returntozero, $userid, $branchid);
        mysqli_stmt_execute($stmt_reset);

        try {
            // Fetch subtotal from cart
            $sql_subtotal = "SELECT SUM(TOTAL_AMT) AS subtotal 
                             FROM orders 
                             WHERE UID = ? AND b_id = ? AND DATE(ORDER_DATE) = CURDATE()";
            $stmt_subtotal = mysqli_prepare($conn, $sql_subtotal);
            mysqli_stmt_bind_param($stmt_subtotal, "ii", $userid, $branchid);
            mysqli_stmt_execute($stmt_subtotal);
            $result_subtotal = mysqli_stmt_get_result($stmt_subtotal);

            if ($result_subtotal) {
                $row_subtotal = mysqli_fetch_assoc($result_subtotal);
                $subtotal = $row_subtotal["subtotal"];
            } else {
                $_SESSION['poserror'] = "Error fetching subtotal";
                header("location: pen_pos.php");
                exit;
            }

            // Fetch order details for printing receipt
            $sql_order_details = "SELECT flavor.flavor, order_details.type, order_details.PRICE, order_details.QTY, order_details.TOTAL, orders.ORDER_NO
                                  FROM order_details
                                  INNER JOIN orders ON order_details.OID = orders.id
                                  INNER JOIN flavor ON flavor.id = order_details.PID
                                  WHERE order_details.b_id = ? AND orders.UID = ? AND DATE(order_details.ORDER_DATE) = CURDATE() AND order_details.type = ?";
            $stmt_order_details = mysqli_prepare($conn, $sql_order_details);
            mysqli_stmt_bind_param($stmt_order_details, "iii", $branchid, $userid, $typeoftype);
            mysqli_stmt_execute($stmt_order_details);
            $result_order_details = mysqli_stmt_get_result($stmt_order_details);

            if ($result_order_details->num_rows > 0) {
                $items = array(); // Initialize array to store items
                $total_amount = 0;

                while ($row = $result_order_details->fetch_assoc()) {
                    // Insert order details
                    $flavor = $row['flavor'];
                    $price = $row['PRICE'];
                    $qty = $row['QTY'];
                    $ORDER_NO = $row['ORDER_NO'];
                    $total = $row['TOTAL'];

                    // Calculate total amount
                    $total_amount += $total;

                    // Add item to the items array
                    $items[] = array('ORDER_NO' => $ORDER_NO, 'flavor' => $flavor, 'PRICE' => $price, 'QTY' => $qty, 'TOTAL' => $total);
                }

                $connector = new WindowsPrintConnector("POS-X Thermal Printer"); // Replace with your printer's name
                $printer = new Printer($connector);
                
                // Print receipt content
                $printer->initialize(); // Initialize printer
                $printer->setJustification(Printer::JUSTIFY_CENTER); // Center-align text
                
                // Header 
                $printer->text("Z-reading\n");
                $printer->text("Cloud Keepers Vape Lounge\n");
                $printer->text("Branch:    $branchname\n");
                
                $printer->text("--------------------------------\n");
                
                // Receipt body
                $printer->setJustification(Printer::JUSTIFY_LEFT); // Left-align text
                date_default_timezone_set('Asia/Manila');
                $printer->text("Date: " . date("Y-m-d H:i:s") . "\n");
                $printer->text(str_pad("ORDER_NO", 12) . str_pad("Quantity", 12) . "TOTAL\n");
                
                foreach ($items as $item) {
                    $ORDER_NO = str_pad($item['ORDER_NO'], 12); // Corrected padding for ORDER_NO
                    $quantity = str_pad($item['QTY'], 12); // Corrected padding for quantity
                    $total = str_pad($item['TOTAL'], 10); // Corrected padding for total
                    $printer->text("$ORDER_NO $quantity $total\n"); // Print formatted line
                }
                
                $printer->text("--------------------------------\n");
                $printer->text("Total:                $subtotal\n");
                $printer->text("Staff:                $username \n");
                
                // Footer
                $printer->text("--------------------------------\n");
                $printer->text("Thank you for shopping with us!\n");
                $printer->text("This is Not An Official Z-reading!\n");
                
                // Cut the receipt
                $printer->cut();
                
                // Close the printer connection
                $printer->close();
                
                

                // Update cart
                $update_cart = "UPDATE cart SET tiny = ? WHERE userid = ? AND b_id = ? AND tiny = 1";
                $stmt_update_cart = mysqli_prepare($conn, $update_cart);
                $tinyy = 0; // Assuming you set tiny to 0 after printing receipt
                mysqli_stmt_bind_param($stmt_update_cart, "iii", $tinyy, $userid, $branchid);
                if (!mysqli_stmt_execute($stmt_update_cart)) {
                    $_SESSION['poserror'] = "Error updating cart";
                    header("location: pen_pos.php");
                    exit;
                }

                $update_car = "UPDATE users SET status = ? WHERE id = ? AND b_id = ? ";
                $stmt_update_car = mysqli_prepare($conn, $update_car);
                mysqli_stmt_bind_param($stmt_update_car, "iii", $typeoftype, $userid, $branchid);
                if (!mysqli_stmt_execute($stmt_update_car)) {
                    $_SESSION['poserror'] = "Error updating cart";
                    header("location: pen_pos.php");
                    exit;
                }

                // Update order_details to reset type
                $typetozero = 0;
                $update_carts = "UPDATE order_details SET type = ? WHERE b_id = ? AND type = 1";
                $stmt_update_carts = mysqli_prepare($conn, $update_carts);
                mysqli_stmt_bind_param($stmt_update_carts, "ii", $typetozero, $branchid);
                if (!mysqli_stmt_execute($stmt_update_carts)) {
                    $_SESSION['poserror'] = "Error updating cart details";
                    header("location: pen_pos.php");
                    exit;
                }

                $_SESSION['possucc'] = "Checkout successful";
                header("location: pen_pos.php");
                exit;
            } else {
                $_SESSION['poserror'] = "No order details found";
                header("location: pen_pos.php");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['poserror'] = "Error printing receipt: " . $e->getMessage();
            header("location: pen_pos.php");
            exit;
        }
    } else {
        $_SESSION['poserror'] = "Error fetching end of day value.";
        header("location: pen_pos.php");
        exit;
    }
}

?>
