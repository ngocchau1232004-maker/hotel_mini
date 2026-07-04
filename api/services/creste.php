<?php
header("Content-Type: application/json");
include "../../config/database.php";

$data=json_decode(file_get_contents("php://input"),true);

if(!$data){

    echo json_encode([
        "success"=>false,
        "message"=>"Sai JSON"
    ]);

    exit();
}

$name=trim($data['service_name']);
$price=intval($data['price']);
$description=$data['description'];

if($name==""){

    echo json_encode([
        "success"=>false,
        "message"=>"Tên dịch vụ bắt buộc"
    ]);

    exit();
}

$sql="INSERT INTO services(service_name,price,description)
VALUES('$name','$price','$description')";

if(mysqli_query($conn,$sql)){

    echo json_encode([
        "success"=>true,
        "service_id"=>mysqli_insert_id($conn),
        "message"=>"Thêm dịch vụ thành công"
    ],JSON_UNESCAPED_UNICODE);

}else{

    echo json_encode([
        "success"=>false,
        "message"=>mysqli_error($conn)
    ]);
}
?>