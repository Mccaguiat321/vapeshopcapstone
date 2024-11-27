<?php
session_start();
include "../database/db_conn.php";
date_default_timezone_set('Asia/Manila');

$branchid = $_SESSION['branch_id'];
$today_date = date('Y-m-d'); // Get the current date in 'YYYY-MM-DD' format

$sql = "SELECT SUM(TOTAL) AS daily_sales
        FROM order_details
        WHERE DATE(ORDER_DATE) = ? AND b_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $today_date, $branchid); // 's' for string, 'i' for integer
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $daily_sales = $row["daily_sales"];
    if ($daily_sales === null) {
        $daily_sales = 0;
    }
    echo $daily_sales;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>