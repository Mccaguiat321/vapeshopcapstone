<?php
include "../database/db_conn.php";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {

    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $flavor = mysqli_real_escape_string($conn, $_POST['flavor']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cost = mysqli_real_escape_string($conn, $_POST['cost']);
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
                $sql = "UPDATE `flavor` SET `rs_id`='$category', `brand`='$brand',`flavor`='$flavor', `description`='$description',`quantity`='$quantity', `cost`='$cost', `price`='$price', `image`='$targetFile' WHERE id = $id";
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

        $sql = "UPDATE `flavor` SET `rs_id`='$category',`brand`='$brand', `flavor`='$flavor', `description`='$description',`quantity`='$quantity', `cost`='$cost' ,`price`='$price' WHERE id = $id";
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
    $sql = "UPDATE flavor SET low = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $low);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['invemessage'] = "Low Range Updated Successfully";
        header("Location: peninventory.php");
    } else {
        $_SESSION['error_inve'] = "Updating Failed " . mysqli_error($conn);
    }



}
if (isset($_POST["transferitems"])) {
    $transfer = $_POST['transquantity']; // Corrected typo in variable name
    $id = $_POST['transferid'];

    $sql = "UPDATE flavor SET quantity = quantity - $transfer WHERE id = '$id'"; // Corrected variable name and added single quotes around $id

    if (mysqli_query($conn, $sql)) {
        $_SESSION['invemessage'] = "Transfer Success";
        header("Location: peninventory.php");
        exit(); // After redirecting, exit to prevent further execution
    } else {
        $_SESSION['error_inve'] = "Transfer Failed: " . mysqli_error($conn);
        exit(); // Exit after redirect
    }
}
if (isset($_POST["addstocks"])) {
    $id = $_POST['idofstocks'];

    $numberofstocks = $_POST['numberofstocks'];

    $sql = "UPDATE flavor SET quantity = quantity + $numberofstocks WHERE id = '$id'"; // Corrected variable name and added single quotes around $id

    if (mysqli_query($conn, $sql)) {
        $_SESSION['invemessage'] = "Success Adding Stocks ";
        header("Location: peninventory.php");
        exit(); // After redirecting, exit to prevent further execution
    } else {
        $_SESSION['error_inve'] = "Transfer Failed: " . mysqli_error($conn);
        exit(); // Exit after redirect
    }



}


?>