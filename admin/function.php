<?php

$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;

if (isset($_POST['addbranch'])) {
    // Ensure $_POST['branch'] is set and not empty
    $branch = $_POST['branch'];

    // Check if an image is uploaded
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
            header("Location: admindashboard.php?name=" . urlencode($branch));            
            exit(); // Stop script execution
        }

        // Check file size (Max 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        if ($_FILES["image"]["size"] > $maxFileSize) {
            $_SESSION['errorinven'] = "Sorry, your file is too large. Maximum allowed size is 5 MB.";
            header("Location: admindashboard.php?name=" . urlencode($branch));            
            exit(); // Stop script execution
        }

        // Allow only specific file formats
        $allowedFileTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedFileTypes)) {
            $_SESSION['errorinven'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: admindashboard.php?name=" . urlencode($branch));            
            exit(); // Stop script execution
        }

        // Proceed with file upload if validations passed
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $_SESSION['errorinven'] = "Sorry, there was an error uploading your file.";
            header("Location: admindashboard.php?name=" . urlencode($branch));            
            exit(); // Stop script execution
        }
    }

    // Assuming $conn is properly initialized elsewhere
    $sql = "SELECT id FROM branch WHERE branch = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $branch);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);
    mysqli_stmt_fetch($stmt);

    // Close the first statement to avoid out-of-sync errors
    mysqli_stmt_close($stmt);

    if (!empty($id)) {
        $_SESSION['error'] = "Branch Already Exists";
    } else {
        // Prepare the INSERT statement with image path
        $sql = "INSERT INTO `branch`(`branch`, `image`) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $branch, $targetFile);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['adminsuccess'] = "Branch created successfully";
        } else {
            $_SESSION['error'] = "Error creating branch: " . mysqli_error($conn);
        }
        
        header("location: admindashboard.php");
        exit(); // Add exit after header redirect
    }
}



if (isset($_POST['updatetherangedaily'])) {
    // Ensure $_POST['numberofdailyranger'] is set and not empty
    if (isset($_POST['numberofdailyranger']) && !empty($_POST['numberofdailyranger'])) {
        $numberofdailyranger = $_POST['numberofdailyranger'];

        // Assuming you want to update the 'daily' column
        $sql = "UPDATE top_sales_ranger SET daily = ? ";  // Adjust 'branch_id' to your actual column name
        $stmt = mysqli_prepare($conn, $sql);
        
        // Bind the parameter. Assuming 'branch' is a string.
        mysqli_stmt_bind_param($stmt, "i", $numberofdailyranger);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['updateed'] = "Set Ranger Sucessfully";
            header("location: pendashboard.php");
           
        } else {
            // Handle errors
            $_SESSION['adminerror'] = "Failed to update the branch.";
        }
    } else {
        // Handle empty input
        $_SESSION['adminerror'] = "Number of daily ranger is required.";
    }
}

if (isset($_POST['overall'])) {
    // Ensure $_POST['numberofdailyranger'] is set and not empty
    if (isset($_POST['numberofoverallranger']) && !empty($_POST['numberofoverallranger'])) {
        $numberofoverallranger = $_POST['numberofoverallranger'];

        // Assuming you want to update the 'daily' column
        $sql = "UPDATE top_sales_ranger SET overall = ? ";  // Adjust 'branch_id' to your actual column name
        $stmt = mysqli_prepare($conn, $sql);
        
        // Bind the parameter. Assuming 'branch' is a string.
        mysqli_stmt_bind_param($stmt, "i", $numberofoverallranger);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['updateedss'] = "Set Ranger Sucessfully";
            header("location: pendashboard.php");
           
        } else {
            // Handle errors
            $_SESSION['adminerror'] = "Failed to update the branch.";
        }
    } else {
        // Handle empty input
        $_SESSION['adminerror'] = "Number of daily ranger is required.";
    }
}
if (isset($_POST['updatetherangemonthly'])) {
    // Ensure $_POST['numberofdailyranger'] is set and not empty
    if (isset($_POST['numberofdailyranger']) && !empty($_POST['numberofdailyranger'])) {
        $numberofdailyranger = $_POST['numberofdailyranger'];

        // Assuming you want to update the 'daily' column
        $sql = "UPDATE top_sales_ranger SET weekly = ? ";  // Adjust 'branch_id' to your actual column name
        $stmt = mysqli_prepare($conn, $sql);
        
        // Bind the parameter. Assuming 'branch' is a string.
        mysqli_stmt_bind_param($stmt, "i", $numberofdailyranger);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['updateeds'] = "Set Ranger Sucessfully";
            header("location: pendashboard.php");
           
        } else {
            // Handle errors
            $_SESSION['adminerror'] = "Failed to update the branch.";
        }
    } else {
        // Handle empty input
        $_SESSION['adminerror'] = "Number of daily ranger is required.";
    }
}
if (isset($_POST['updatetherangeweekly'])) {
    // Ensure $_POST['numberofdailyranger'] is set and not empty
    if (isset($_POST['numberofdailyranger']) && !empty($_POST['numberofdailyranger'])) {
        $numberofdailyranger = $_POST['numberofdailyranger'];

        // Assuming you want to update the 'daily' column
        $sql = "UPDATE top_sales_ranger SET monthly = ? ";  // Adjust 'branch_id' to your actual column name
        $stmt = mysqli_prepare($conn, $sql);
        
        // Bind the parameter. Assuming 'branch' is a string.
        mysqli_stmt_bind_param($stmt, "i", $numberofdailyranger);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['updateedssaw'] = "Set Ranger Sucessfully";
            header("location: pendashboard.php");
           
        } else {
            // Handle errors
            $_SESSION['adminerror'] = "Failed to update the branch.";
        }
    } else {
        // Handle empty input
        $_SESSION['adminerror'] = "Number of daily ranger is required.";
    }
}
if (isset($_POST['update'])) {
    $branch = $_POST['branch']; // Input field name
    $id = $_POST['id'];

    // Default image path
    $defaultImage = "../images/27002.jpg";
    $imageUploaded = !empty($_FILES["image"]["name"]);
    $targetFile = $defaultImage; // Start with the default image path

    if ($imageUploaded) {
        $targetDirectory = "../images/";
        $targetFile = $targetDirectory . basename($_FILES["image"]["name"]); // Create target file path
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check === false) {
            $_SESSION['errorinven'] = "File is not an image.";
            header("Location: admindashboard.php");
            exit();
        }

        // Check file size (Max 5MB)
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        if ($_FILES["image"]["size"] > $maxFileSize) {
            $_SESSION['errorinven'] = "Sorry, your file is too large. Maximum allowed size is 5 MB.";
            header("Location: admindashboard.php");
            exit();
        }

        // Allow only specific file formats
        $allowedFileTypes = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $allowedFileTypes)) {
            $_SESSION['errorinven'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            header("Location: admindashboard.php");
            exit();
        }

        // Proceed with file upload if validations passed
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $_SESSION['errorinven'] = "Sorry, there was an error uploading your file.";
            header("Location: admindashboard.php");
            exit();
        }
    } else {
        // If no new image is uploaded, keep the existing one
        $sqlSelect = "SELECT image FROM branch WHERE id = ?";
        $stmtSelect = mysqli_prepare($conn, $sqlSelect);
        mysqli_stmt_bind_param($stmtSelect, "i", $id);
        mysqli_stmt_execute($stmtSelect);
        mysqli_stmt_bind_result($stmtSelect, $existingImage);
        mysqli_stmt_fetch($stmtSelect);
        mysqli_stmt_close($stmtSelect);
        
        // Ensure the existing image path is valid or fallback to default
        $targetFile = !empty($existingImage) ? $existingImage : $defaultImage;
    }

    // Debugging: Check paths before the update
    if (empty($targetFile)) {
        $_SESSION['errorinven'] = "Image path cannot be empty.";
        header("Location: admindashboard.php");
        exit();
    }

    // Check if targetFile is a valid path
    if (!file_exists($targetFile) && $targetFile !== $defaultImage) {
        $_SESSION['errorinven'] = "Target file path is invalid: " . htmlspecialchars($targetFile);
        header("Location: admindashboard.php");
        exit();
    }

    // Update branch details
    $sql = "UPDATE branch SET branch = ?, image = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $branch, $targetFile, $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Branch updated successfully.";
    } else {
        $_SESSION['errorinven'] = "Error updating branch: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    header("Location: admindashboard.php"); // Redirect after processing
    exit();
}





?>