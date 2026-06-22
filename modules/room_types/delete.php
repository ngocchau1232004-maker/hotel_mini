<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    /** @var mysqli $conn */ 
    $conn = $conn;

    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM room_types WHERE room_type_id=$id");

    header("Location:index.php");
?>