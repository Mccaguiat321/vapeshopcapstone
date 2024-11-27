<?php
require 'database/db_conn.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updatepassword'])) {
    $acc_id = $_POST['idnanguser']; // Get user ID from hidden input
    $changepassword = $_POST['password'];

    // Hash the new password
    $hash = password_hash($changepassword, PASSWORD_BCRYPT);
    $sql = "UPDATE `users` SET `password`=? WHERE id = ?"; // Use placeholders for prepared statement
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $hash, $acc_id); // Bind parameters
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $_SESSION['invemessage'] = "Successfully updated password. Log in to your account.";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating password. Please try again.";
            header("Location: recovery.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Database error. Please try again.";
        header("Location: recovery.php");
        exit();
    }
}
?>