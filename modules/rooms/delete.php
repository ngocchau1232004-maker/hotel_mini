<?php
    include '../../includes/auth.php';
    include '../../config/database.php';

    $id = $_GET['id'];

    $sql = "DELETE FROM rooms
            WHERE room_id = '$id'";

    mysqli_query($conn, $sql);

    header("Location:index.php");
    exit();
?>