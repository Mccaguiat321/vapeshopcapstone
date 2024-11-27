<?php
session_start();
include "../database/db_conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['itemid'])) {
        $itemid = $_POST['itemid'];
        $branchid = $_SESSION['branch'];
        $staff_id = $_SESSION['user_id'];

        // Prepare and execute the update query
        $sql = "UPDATE users SET status = 0 WHERE id = ? AND b_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $itemid, $branchid);
        
        if (mysqli_stmt_execute($stmt)) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'invalid';
    }
}

// Close the statement and connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
