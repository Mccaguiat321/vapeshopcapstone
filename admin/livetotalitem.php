<?php
session_start();
include "../database/db_conn.php";
$branchid = $_SESSION['branch_id'];
$sql = "SELECT COUNT(*) AS total_items FROM flavor WHERE f_b_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $branchid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_items = $row["total_items"]; // Assign total_items to a variable
    echo $total_items;
    if ($total_items === null) { // Check if total_items is null
        $total_items = 0;
    }
} else {
    echo "Error: " . mysqli_error($conn);
}
?>