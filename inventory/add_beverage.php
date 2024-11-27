<?php
include "../database/db_conn.php";


$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;
if (isset($_POST["sad"])) {
    $category_name = $_POST['beverages'];

    // Prepare the SQL statement to avoid SQL injection
    $categoryInsertQuery = $conn->prepare("INSERT INTO category (category, c_b_id) VALUES (?, ?)");
    
    // Bind parameters to the prepared statement
    $categoryInsertQuery->bind_param("si", $category_name, $branchid); // "si" means string, integer

    if ($categoryInsertQuery->execute()) {
        $_SESSION['invemessage'] = "Category Successfully Created";
        header("Location: peninventory.php");
    } else {
        $_SESSION['invemessage'] = "Failed to add New Category: " . $categoryInsertQuery->error;
        $_SESSION['invemessage_type'] = 'error';
        header("Location: peninventory.php");
        exit();
    }
}



if (isset($_POST["addflavor"])) {
    // Escaping user inputs using $_POST
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $option = mysqli_real_escape_string($conn, $_POST['option']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cost = mysqli_real_escape_string($conn, $_POST['cost']);
    $manu = mysqli_real_escape_string($conn, $_POST['manu']);
    $expi = mysqli_real_escape_string($conn, $_POST['expi']);
    $status= mysqli_real_escape_string($conn, $_POST['status']);
    $lows= mysqli_real_escape_string($conn, $_POST['lows']);
   
    $currentDate = new DateTime();  
    $manufacturedDate = new DateTime($manu);
    $expirationDate = new DateTime($expi);

    // Check if manufactured date is in the future
    if ($manufacturedDate > $currentDate) {
        $_SESSION['errorinven'] = "Manufactured date cannot be in the future.";
        header("Location: peninventory.php?name=" . urlencode($name) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price) . "&option=" . urlencode($option) . "&brand=" . urlencode($brand) . "&description=" . urlencode($description) . "&cost=" . urlencode($cost) . "&manu=" . urlencode($manu) . "&expi=" . urlencode($expi). "&status=" . urlencode($status). "&lows=" . urlencode($lows));
        exit(); // Stop script execution
    }
    if ($manufacturedDate > $currentDate) {
   
        $_SESSION['errorinven'] = "Expiration date cannot be in the past.";
        header("Location: peninventory.php?name=" . urlencode($name) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price) . "&option=" . urlencode($option) . "&brand=" . urlencode($brand) . "&description=" . urlencode($description) . "&cost=" . urlencode($cost) . "&manu=" . urlencode($manu) . "&expi=" . urlencode($expi). "&status=" . urlencode($status). "&lows=" . urlencode($lows));        exit(); // Stop script execution
    }


    // Check if expiration date is in the past
    if ( $cost > $price) {
        $_SESSION['errorinven'] = "Cost cannot be higher than the price.";
        header("Location: peninventory.php?name=" . urlencode($name) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price) . "&option=" . urlencode($option) . "&brand=" . urlencode($brand) . "&description=" . urlencode($description) . "&cost=" . urlencode($cost) . "&manu=" . urlencode($manu) . "&expi=" . urlencode($expi). "&status=" . urlencode($status). "&lows=" . urlencode($lows));        exit(); // Stop script execution
    }

    // Handle image upload only if an image was uploaded
    $imageUploaded = !empty($_FILES["image"]["name"]);
    $targetFile = "../images/27002.jpg"; // Default image

    if ($imageUploaded) {
        $targetDirectory = "../images/";
        $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['errorinven'] = "File is not an image.";
            header("Location: peninventory.php?name=" . urlencode($name) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price) . "&option=" . urlencode($option) . "&brand=" . urlencode($brand) . "&description=" . urlencode($description) . "&cost=" . urlencode($cost) . "&manu=" . urlencode($manu) . "&expi=" . urlencode($expi). "&status=" . urlencode($status). "&lows=" . urlencode($lows));            exit(); // Stop script execution
        }

        // Check file size (Max 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        if ($_FILES["image"]["size"] > $maxFileSize) {
            $_SESSION['errorinven'] = "Sorry, your file is too large. Maximum allowed size is 5 MB.";
            header("Location: peninventory.php?name=" . urlencode($name) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price) . "&option=" . urlencode($option) . "&brand=" . urlencode($brand) . "&description=" . urlencode($description) . "&cost=" . urlencode($cost) . "&manu=" . urlencode($manu) . "&expi=" . urlencode($expi). "&status=" . urlencode($status). "&lows=" . urlencode($lows));            exit(); // Stop script execution
        }

        // Allow only specific file formats
        $allowedFileTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedFileTypes)) {
            $_SESSION['errorinven'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: peninventory.php?name=" . urlencode($name) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price) . "&option=" . urlencode($option) . "&brand=" . urlencode($brand) . "&description=" . urlencode($description) . "&cost=" . urlencode($cost) . "&manu=" . urlencode($manu) . "&expi=" . urlencode($expi). "&status=" . urlencode($status). "&lows=" . urlencode($lows));            exit(); // Stop script execution
        }

        // Proceed with file upload if validations passed
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $_SESSION['errorinven'] = "Sorry, there was an error uploading your file.";
            header("Location: peninventory.php?name=" . urlencode($name) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price) . "&option=" . urlencode($option) . "&brand=" . urlencode($brand) . "&description=" . urlencode($description) . "&cost=" . urlencode($cost) . "&manu=" . urlencode($manu) . "&expi=" . urlencode($expi). "&status=" . urlencode($status). "&lows=" . urlencode($lows));            exit(); // Stop script execution
        }
    }

    // Insert data into the database
    $orderInsertQuery = "INSERT INTO `flavor` 
        (rs_id, brand, flavor, description, quantity, cost, price, manufactured_date, date, image,low,status, f_b_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";

    // Prepare statement to avoid SQL injection
    $stmt = $conn->prepare($orderInsertQuery);

    // Bind parameters
    $stmt->bind_param("isssisisssiii", $option, $brand, $name, $description, $quantity, $cost, $price, $manu, $expi, $targetFile,$lows,$status, $branchid);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['invemessage'] = "New Flavor Added Successfully";
        header("Location: peninventory.php");
        exit(); // Stop script execution
    } else {
        $_SESSION['errorinven'] = "Error: " . $stmt->error;
        header("Location: peninventory.php?name=" . urlencode($name) . "&quantity=" . urlencode($quantity) . "&price=" . urlencode($price) . "&option=" . urlencode($option) . "&brand=" . urlencode($brand) . "&description=" . urlencode($description) . "&cost=" . urlencode($cost) . "&manu=" . urlencode($manu) . "&expi=" . urlencode($expi). "&status=" . urlencode($status). "&lows=" . urlencode($lows));        exit(); // Stop script execution
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $flavor = mysqli_real_escape_string($conn, $_POST['flavor']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cost = mysqli_real_escape_string($conn, $_POST['cost']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $manu = mysqli_real_escape_string($conn, $_POST['manu']);
    $expi = mysqli_real_escape_string($conn, $_POST['expi']);
    $lows= mysqli_real_escape_string($conn, $_POST['lows']);
    $currentDate = new DateTime();  
    $manufacturedDate = new DateTime($manu);
    $expirationDate = new DateTime($expi);

    // Check if manufactured date is in the future
    if ($manufacturedDate > $currentDate) {
        $_SESSION['errorinven'] = "Manufactured date cannot be in the future.";
        header("Location: peninventory.php");
        exit(); // Stop script execution
    }
    if ($manufacturedDate > $currentDate) {
   
        $_SESSION['errorinven'] = "Expiration date cannot be in the past.";
        header("Location: peninventory.php");
        exit(); // Stop script execution
    }


    // Check if expiration date is in the past
    if ( $cost > $price) {
        $_SESSION['errorinven'] = "Cost cannot be higher than the price.";
        header("Location: peninventory.php");
        exit(); // Stop script execution
    }
    $targetDirectory = "../images/";
    $uploadOk = 1;
    if (!empty($_FILES["image"]["name"])) {
        $targetFile = $targetDirectory . basename($_FILES["image"]["name"]);
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        $maxFileSize = 5 * 1024 * 1024;
        if ($_FILES["image"]["size"] > $maxFileSize) {
            echo "Sorry, your file is too large. Maximum allowed size is 5 MB.";
            $uploadOk = 0;
        }
        $allowedFileTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array(strtolower(pathinfo($targetFile, PATHINFO_EXTENSION)), $allowedFileTypes)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Only proceed if there are no upload errors
        if ($uploadOk) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                // Update the database with the new image path
                $sql = "UPDATE `flavor` SET `rs_id`='$category', `brand`='$brand',`flavor`='$flavor', `description`='$description',`quantity`='$quantity', `cost`='$cost', `price`='$price', `image`='$targetFile', `manufactured_date`='$manu', `date`='$expi',`low` = '$lows',`status`= '$status' WHERE id = $id";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    $_SESSION['invemessage'] = "Item Updated Successfully";
                    header("Location: peninventory.php");
                } else {
                    $_SESSION['error_inve'] = "Failed: " . mysqli_error($conn);

                }
            } else {

                $_SESSION['error_inve'] = "Sorry, there was an error uploading your file.";
            }
        }
    } else {

        $sql = "UPDATE `flavor` SET `rs_id`='$category',`brand`='$brand', `flavor`='$flavor', `description`='$description',`quantity`='$quantity', `cost`='$cost' ,`price`='$price', `manufactured_date`='$manu', `date`='$expi',`low` = '$lows',`status`= '$status' WHERE id = $id";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $_SESSION['invemessage'] = "Item Updated Successfully";
            header("Location: peninventory.php");
        } else {
            $_SESSION['error_inve'] = "Failed: " . mysqli_error($conn);
        }
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["lowss"])) {
    $low = $_POST['low'];
    
    // Ensure some checkboxes were selected
    if (!empty($_POST['selected_ids'])) {
        $selected_ids = $_POST['selected_ids']; // Array of selected IDs

        // Prepare the update statement with a placeholder for the ID
        $sql = "UPDATE flavor SET low = ? WHERE id = ? AND f_b_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        // Loop through each selected ID and update the low stock value
        foreach ($selected_ids as $id) {
            mysqli_stmt_bind_param($stmt, "iii", $low, $id,$branchid); // Bind the new low value and the flavor ID
            if (!mysqli_stmt_execute($stmt)) {
                $_SESSION['error_inve'] = "Updating Failed for ID $id: " . mysqli_error($conn);
                break;
            }
        }

        // If no errors occurred, set the success message
        if (empty($_SESSION['error_inve'])) {
            $_SESSION['invemessage'] = "Low Range Updated Successfully for selected flavors.";
        }

        // Redirect back to the inventory page
        header("Location: peninventory.php");
    } else {
        // No flavors were selected
        $_SESSION['error_invea'] = "Please select at least one flavor to update.";
    }
}
if (isset($_POST["transferitems"])) {
    $transfer = intval($_POST['transquantity']); // Quantity to transfer
    $id = intval($_POST['transferid']); // Item ID
    $flavor = $_POST['flavor']; // Product name
    $price = $_POST['price']; // Product name
    $option = intval($_POST['option']); // Branch ID to transfer to

    // Check if there is an item with the given flavor name in the selected branch
    $sql_check = "SELECT quantity FROM flavor WHERE flavor = ? AND f_b_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $flavor, $option);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // If no item exists in the branch, set an error message
        $_SESSION['error_inveaaa'] = "No item with the flavor '$flavor' found in the selected branch.";
    } else {
        // Fetch the current quantity of the flavor in the selected branch
        $row = $result_check->fetch_assoc();
        $current_quantity = $row['quantity'];

        // Check if we have enough quantity to transfer
        if ($current_quantity < $transfer) {
            $_SESSION['error_inveaaa'] = "Not enough quantity to transfer.";
        } else {
            // Update the quantity in the current branch
            $sql_update_current = "UPDATE flavor SET quantity = quantity - ? WHERE id = ? AND f_b_id = ?";
            $stmt_update_current = $conn->prepare($sql_update_current);
            $stmt_update_current->bind_param("isi", $transfer, $id, $branchid); // Assuming $branchid is defined somewhere
            $stmt_update_current->execute();

            // Update the quantity in the target branch
            $sql_update_target = "UPDATE flavor SET quantity = quantity + ? WHERE flavor = ? AND f_b_id = ?";
            $stmt_update_target = $conn->prepare($sql_update_target);
            $stmt_update_target->bind_param("isi", $transfer, $flavor, $option);
            $stmt_update_target->execute();

            // Insert into transfer history after successful update
            $sql_insert_history = "INSERT INTO transfer_history (product_name, send_to,quantity,price ,date, b_id) VALUES (?,?,?,?, NOW(), ?)";
            $stmt_insert = $conn->prepare($sql_insert_history);
            $stmt_insert->bind_param("siiii", $flavor, $option,$transfer,$price, $branchid);

            if ($stmt_insert->execute()) {
                $_SESSION['invemessage'] = "Transfer Success"; // Use a meaningful session variable
            } else {
                $_SESSION['error_inveaaa'] = "Error logging transfer: " . $conn->error;
            }
        }
    }

    // Redirect to the inventory page
    header("Location: peninventory.php");
    exit(); // Prevent further execution
}

if (isset($_POST["addstocks"])) {

    $id = $_POST['idofstocks'];
    $numberofstocks = $_POST['numberofstocks'];

    $date = date('Y-m-d H:i:s'); // Use current date and time

    // Prepare the SQL queries
    $sql_update = "UPDATE flavor SET quantity = quantity + ? WHERE id = ? AND f_b_id = ?";
    $sql_insert_history = "INSERT INTO add_stock_history (product_name, quantity, date, b_id) VALUES (?, ?, ?, ?)";

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Prepare and execute the update query
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iii", $numberofstocks, $id,$branchid);
        $stmt_update->execute();

        // Prepare and execute the insert query for stock historys
        $stmt_insert = $conn->prepare($sql_insert_history);
        $stmt_insert->bind_param("sisi", $id, $numberofstocks, $date, $branchid);
        $stmt_insert->execute();

        // Commit transaction
        mysqli_commit($conn);

        // Set success message and redirect
        $_SESSION['invemessage'] = "Success Adding Stocks";
        header("Location: peninventory.php");
        exit();

    } catch (Exception $e) {
        // Rollback transaction in case of error
        mysqli_rollback($conn);
        $_SESSION['error_inve'] = "Transfer Failed: " . $e->getMessage();
        header("Location: peninventory.php");
        exit();
    }
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change"])) {
    $id = mysqli_real_escape_string($conn, $_POST["itemId"]);
    $editedItem = mysqli_real_escape_string($conn, $_POST['editedItem']);

    $sql = "UPDATE `category` SET `category`='$editedItem' WHERE id = $id";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        $_SESSION['invemessage'] = "Edit Successfully";
        header("Location: peninventory.php");
        exit();
    } else {
        $_SESSION['error_inve'] = "Editing Failed: " . mysqli_error($conn);

    }
}






?>