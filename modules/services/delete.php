<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */
    $conn = $conn;

    $id = $_GET['id'];

    mysqli_query($conn,"DELETE FROM services WHERE service_id=$id");

    header("Location:index.php");
?>