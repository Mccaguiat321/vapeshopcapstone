<?php
session_start();
include "../database/db_conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['itemid'])) {
        $itemid = $_POST['itemid'];
        $branchid = $_SESSION['branch'];
        $staff_id = $_SESSION['user_id'];

        // Prepare and execute delete query
        $sql = "DELETE FROM cart WHERE id = ? AND userid = ? AND b_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $itemid, $staff_id, $branchid);
        
        if (mysqli_stmt_execute($stmt)) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'invalid';
    }
}
?>
