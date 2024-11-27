<?php
include "../database/db_conn.php";
$branchid = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : null;


if (isset($_POST["createacc"])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE user_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);
    mysqli_stmt_fetch($stmt);

    if (!empty($id)) {
        // Redirect with error message if email already exists
        header("Location: accdash.php?msg=UserName Already Exists");
        exit(); // Stop further execution
    } else {
        // Insert new user into the database
        $sql = "INSERT INTO `users`( `user_name`,`password`, `role`, `email`,`b_id`) VALUES (?,?,?,?,?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $hash, $role, $email, $branchid);
        mysqli_stmt_execute($stmt);

        $_SESSION['invemessage'] = "Account Created Successfully";
        header("Location: accountdashboard.php?msg=Account Created Successfully");
        exit(); // Stop further execution
    }
}
?>