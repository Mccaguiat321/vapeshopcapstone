<?php
include "../database/db_conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change"])) {
    $id = mysqli_real_escape_string($conn, $_POST["itemId"]);
    $editedItem = mysqli_real_escape_string($conn, $_POST['editedItem']);

    $sql = "UPDATE `category` SET `category`='$editedItem' WHERE id = $id";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        $_SESSION['invemessage'] = "Edit Successfully";
        header("Location: peninventory.php");
        exit();
    } else {
        $_SESSION['error_inve'] = "Editing Failed: " . mysqli_error($conn);

    }
}
?>