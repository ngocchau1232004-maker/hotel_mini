<?php
    header("Content-Type: application/json");
    include "../../config/database.php";

    $data=json_decode(file_get_contents("php://input"),true);

    $id=intval($data['service_id']);

    $name=$data['service_name'];

    $price=intval($data['price']);

    $description=$data['description'];

    $result=mysqli_query($conn,"
    SELECT *
    FROM services
    WHERE service_id='$id'");

    if(mysqli_num_rows($result)==0){

        echo json_encode([
            "success"=>false,
            "message"=>"Không tồn tại"
        ]);

        exit();
    }

    $sql="UPDATE services
    SET
    service_name='$name',
    price='$price',
    description='$description'
    WHERE service_id='$id'";

    mysqli_query($conn,$sql);

    echo json_encode([
        "success"=>true,
        "message"=>"Cập nhật thành công"
    ],JSON_UNESCAPED_UNICODE);
?>