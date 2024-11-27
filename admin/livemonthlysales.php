<?php
session_start();
include "../database/db_conn.php";
date_default_timezone_set('Asia/Manila');
$branchid = $_SESSION['branch_id'];

$startOfMonth = date("Y-m-01");
$endOfMonth = date("Y-m-t");

$sql = "SELECT SUM(TOTAL) AS monthly_sales
        FROM order_details
        WHERE DATE(ORDER_DATE) BETWEEN ? AND ? AND b_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $startOfMonth, $endOfMonth, $branchid); // 's' for string, 'i' for integer
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $monthly_sales = $row["monthly_sales"];
    if ($monthly_sales === null) {
        $monthly_sales = 0;
    }
    echo $monthly_sales;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>