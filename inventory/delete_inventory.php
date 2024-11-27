<?php
session_start();
include "../database/db_conn.php";
$id = $_GET["id"];
$sql = "DELETE FROM `flavor` WHERE id = $id ";
$result = mysqli_query($conn, $sql);

if ($result) {

    header("Location: peninventory.php");
    $_SESSION['invemessage'] = "Flavor Deleted Successfully";
} else {

    $_SESSION['error_inve'] = "Failed: " . mysqli_error($conn);
}