<?php
session_start();
include "../database/db_conn.php";

$branchid = $_SESSION['branch_id'];

// Query to get total sales
$sql_sales = "SELECT SUM(TOTAL) AS total_sales
              FROM order_details
              WHERE b_id = $branchid";

$result_sales = mysqli_query($conn, $sql_sales);

if (!$result_sales) {
    die("Error in SQL query for daily sales: " . mysqli_error($conn));
}

$row_sales = mysqli_fetch_assoc($result_sales);
$total_sales = $row_sales['total_sales'] ?? 0; // If total_sales is null, set it to 0

// Query to get total margin
$sql_margin = "SELECT SUM(MARGIN) AS total_margin
               FROM order_details
               WHERE b_id = $branchid";

$result_margin = mysqli_query($conn, $sql_margin);

if (!$result_margin) {
    die("Error in SQL query for daily margin: " . mysqli_error($conn));
}

$row_margin = mysqli_fetch_assoc($result_margin);

// Calculate margin
$margin = $total_sales - ($row_margin['total_margin'] ?? 0); // If total_margin is null, set it to 0

echo $margin;
?>