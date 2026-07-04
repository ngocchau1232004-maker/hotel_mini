<?php
    header("Content-Type: application/json");
    include "../../config/database.php";

    $data=json_decode(file_get_contents("php://input"),true);

    $id=intval($data['service_id']);

    $result=mysqli_query($conn,"
    SELECT *
    FROM services
    WHERE service_id='$id'");

    if(mysqli_num_rows($result)==0){

        echo json_encode([
            "success"=>false,
            "message"=>"Không tồn tại dịch vụ"
        ]);

        exit();
    }

    mysqli_query($conn,"
    DELETE FROM services
    WHERE service_id='$id'
    ");

    echo json_encode([
        "success"=>true,
        "message"=>"Đã xóa dịch vụ"
    ],JSON_UNESCAPED_UNICODE);

?>