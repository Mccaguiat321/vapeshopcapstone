<?php
// start_of_day.php

include "../database/db_conn.php";


$branchid = $_SESSION['branch'] ?? 0;
$staff_id = $_SESSION['user_id'] ?? 0;

$sql = "SELECT sod AS start_of_day FROM `users` WHERE id = ? AND b_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $staff_id, $branchid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        echo $row["start_of_day"];
    } else {
        echo "No start of day value found for the current user.";
    }
} else {
    echo "Error: Unable to retrieve start of day value";
}
?>
