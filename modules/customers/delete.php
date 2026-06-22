<?php
    include '../../includes/auth.php';  
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    $id = $_GET['id'];
    $sql = "DELETE FROM customers WHERE customer_id = '$id'";

    mysqli_query($conn, $sql);

    header("Location:index.php");
    exit();
?>