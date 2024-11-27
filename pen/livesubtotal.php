<?php
session_start();
include "../database/db_conn.php";

$branchid = $_SESSION['branch'];
$staff_id = $_SESSION['user_id'];
$tiny = 1;

$sql = "SELECT COALESCE(SUM(total), 0) AS subtotal FROM `cart` WHERE userid = ? AND b_id = ? AND tiny = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iii", $staff_id, $branchid, $tiny);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo $row["subtotal"]; // This will output 0 if SUM(total) returns NULL
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
