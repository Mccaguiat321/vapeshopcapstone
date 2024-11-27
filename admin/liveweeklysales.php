<?php
session_start();
include "../database/db_conn.php";
date_default_timezone_set('Asia/Manila');
$branchid = $_SESSION['branch_id'];

$startOfWeek = date("Y-m-d", strtotime('monday this week'));
$endOfWeek = date("Y-m-d", strtotime('sunday this week'));

$sql = "SELECT SUM(TOTAL) AS weekly_sales
        FROM order_details
        WHERE DATE(ORDER_DATE) BETWEEN ? AND ? AND b_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $startOfWeek, $endOfWeek, $branchid); // 's' for string, 'i' for integer
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $weekly_sales = $row["weekly_sales"];
    if ($weekly_sales === null) {
        $weekly_sales = 0;
    }
    echo $weekly_sales;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>