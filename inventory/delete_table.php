<?php
session_start();
include "../database/db_conn.php";
$id = $_GET["id"];
$sql = "DELETE FROM `category` WHERE id = $id";
$result = mysqli_query($conn, $sql);

if ($result) {
    $_SESSION['invemessage'] = "Category deleted successfully";
    header("Location: peninventory.php? ");
} else {

    $_SESSION['error_inve'] = "Failed: " . mysqli_error($conn);
}