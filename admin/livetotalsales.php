<?php
session_start();
include "../database/db_conn.php";
date_default_timezone_set('Asia/Manila');
$branchid = $_SESSION['branch_id'];

$sql = "SELECT SUM(TOTAL) AS totalsales
        FROM order_details
        WHERE b_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $branchid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalsales = $row["totalsales"];

    if ($totalsales === null) {
        $totalsales = 0;
    }
    echo $totalsales;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>