<?php

//* |=============================================================
//* |   HOTEL MINI API - Edit Room Type
//* | File: api/room_types/edit.php
//* | POST: http://localhost/hotel_mini/api/room_types/edit.php
//* |=============================================================

require_once '../auth.php';

/*
|--------------------------------------------------------------------------
| GET hoặc POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = getJson();

    $id          = isset($data['id']) ? intval($data['id']) : 0;
    $type_name   = isset($data['type_name']) ? str($data['type_name']) : "";
    $price       = isset($data['price']) ? intval($data['price']) : 0;
    $max_people  = isset($data['max_people']) ? intval($data['max_people']) : 0;
    $description = isset($data['description']) ? str($data['description']) : "";

}
elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $id          = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $type_name   = isset($_GET['type_name']) ? str($_GET['type_name']) : "";
    $price       = isset($_GET['price']) ? intval($_GET['price']) : 0;
    $max_people  = isset($_GET['max_people']) ? intval($_GET['max_people']) : 0;
    $description = isset($_GET['description']) ? str($_GET['description']) : "";

}
else{

    error("Method không được hỗ trợ.",405);

}

/*
|--------------------------------------------------------------------------
| Validate
|--------------------------------------------------------------------------
*/

if($id <= 0){
    error("ID loại phòng không hợp lệ.");
}

if($type_name == ""){
    error("Tên loại phòng không được để trống.");
}

if($price <= 0){
    error("Giá phòng phải lớn hơn 0.");
}

if($max_people <= 0){
    error("Số người tối đa phải lớn hơn 0.");
}

/*
|--------------------------------------------------------------------------
| Kiểm tra tồn tại
|--------------------------------------------------------------------------
*/

$sql = "SELECT *
        FROM room_types
        WHERE room_type_id='$id'
        LIMIT 1";

$query = mysqli_query($conn,$sql);

if(mysqli_num_rows($query)==0){
    error("Không tìm thấy loại phòng.");
}

/*
|--------------------------------------------------------------------------
| Kiểm tra trùng tên
|--------------------------------------------------------------------------
*/

$sql = "SELECT room_type_id
        FROM room_types
        WHERE type_name='$type_name'
        AND room_type_id<>'$id'
        LIMIT 1";

$query = mysqli_query($conn,$sql);

if(mysqli_num_rows($query)>0){
    error("Tên loại phòng đã tồn tại.");
}

/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

$sql = "UPDATE room_types SET

            type_name='$type_name',
            price='$price',
            max_people='$max_people',
            description='$description'

        WHERE room_type_id='$id'";

if(!mysqli_query($conn,$sql)){
    error(mysqli_error($conn),500);
}

/*
|--------------------------------------------------------------------------
| Lấy dữ liệu mới
|--------------------------------------------------------------------------
*/

$sql = "SELECT *
        FROM room_types
        WHERE room_type_id='$id'
        LIMIT 1";

$query = mysqli_query($conn,$sql);

$row = mysqli_fetch_assoc($query);

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

success([
    "room_type"=>$row
],"Cập nhật loại phòng thành công.");

?>