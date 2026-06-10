<?php

    $conn = mysqli_connect(
        "localhost",
        "root",
        "",
        "hotel_management"
    );

    if(!$conn){
        die("Kết nối thất bại");
    }
    
?>