<?php
session_start();
include "database/db_conn.php";
if (isset($_POST['uname'], $_POST['password'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $uname = validate($_POST['uname']);
    $pass = validate($_POST['password']);

    $sql = "SELECT users.id ,user_name, password, role, branch.id ,branch.branch FROM users INNER JOIN branch ON branch.id =  users.b_id WHERE users.user_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $uname);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $username, $hashpass, $role, $branch , $branchname);
    mysqli_stmt_fetch($stmt);

    if (password_verify($pass, $hashpass)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['branch'] = $branch;
        $_SESSION['branchname'] = $branchname;
        switch ($role) {
            case 'superadmin':
                $_SESSION['adminsuccess'] = "Welcome Admin";
                header("Location: admin/admindashboard.php");
                exit();
            case 'staff':
                $_SESSION['possucc'] = "Welcome " . $_SESSION['user_name'];
                header("Location: pen/pen_pos.php");
                exit();
            default:
            $_SESSION['error'] = "Invalid User Role.";
                header("Location: index.php");
                exit();
        }
    } else {
        $_SESSION['error'] = "Invalid username or password.";
        header("Location: index.php");
        exit();
    }
} else {

    header("Location: index.php");
    exit();
}
?>