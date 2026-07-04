<?php
    header("Content-Type: application/json");
    include "../../config/database.php";

    $result=mysqli_query($conn,"
    SELECT *
    FROM services
    ORDER BY service_id DESC");

    $data=[];

    while($row=mysqli_fetch_assoc($result)){
        $data[]=$row;
    }

    echo json_encode([
        "success"=>true,
        "total"=>count($data),
        "data"=>$data
    ],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
?>