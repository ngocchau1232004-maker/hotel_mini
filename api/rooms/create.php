<?php

//* |===================================================================
//* |     HOTEL MINI API Room Type Create
//* |  File: api/rooms/create.php
//* |  POST: http://localhost/hotel_mini/api/rooms/create.php
//* |===================================================================

require_once '../auth.php';

/*
|--------------------------------------------------------------------------
| GET hoặc POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = getJson();

    $room_number = isset($data['room_number']) ? str($data['room_number']) : "";
    $room_type_id = isset($data['room_type_id']) ? intval($data['room_type_id']) : 0;
    $status = isset($data['status']) ? str($data['status']) : "";

}
elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $room_number = isset($_GET['room_number']) ? str($_GET['room_number']) : "";
    $room_type_id = isset($_GET['room_type_id']) ? intval($_GET['room_type_id']) : 0;
    $status = isset($_GET['status']) ? str($_GET['status']) : "";

}
else{

    error("Method không được hỗ trợ.",405);

}

/*
|--------------------------------------------------------------------------
| Validate
|--------------------------------------------------------------------------
*/

if($room_number==""){
    error("Vui lòng nhập số phòng.");
}

if($room_type_id<=0){
    error("Loại phòng không hợp lệ.");
}

if($status==""){
    $status="Trống";
}

$allowStatus=[
    "Trống",
    "Đã đặt",
    "Đang thuê",
    "Bảo trì"
];

if(!in_array($status,$allowStatus)){
    error("Trạng thái không hợp lệ.");
}

/*
|--------------------------------------------------------------------------
| Kiểm tra trùng số phòng
|--------------------------------------------------------------------------
*/

$sql="SELECT room_id
      FROM rooms
      WHERE room_number='$room_number'
      LIMIT 1";

$query=mysqli_query($conn,$sql);

if(mysqli_num_rows($query)>0){
    error("Số phòng đã tồn tại.");
}

/*
|--------------------------------------------------------------------------
| Kiểm tra loại phòng
|--------------------------------------------------------------------------
*/

$sql="SELECT *
      FROM room_types
      WHERE room_type_id='$room_type_id'
      LIMIT 1";

$query=mysqli_query($conn,$sql);

if(mysqli_num_rows($query)==0){
    error("Loại phòng không tồn tại.");
}

/*
|--------------------------------------------------------------------------
| Insert
|--------------------------------------------------------------------------
*/

$sql="INSERT INTO rooms(

        room_number,
        room_type_id,
        status

      )

      VALUES(

        '$room_number',
        '$room_type_id',
        '$status'

      )";

if(!mysqli_query($conn,$sql)){
    error(mysqli_error($conn),500);
}

$id=mysqli_insert_id($conn);

/*
|--------------------------------------------------------------------------
| Lấy dữ liệu vừa thêm
|--------------------------------------------------------------------------
*/

$sql="SELECT

        r.room_id,
        r.room_number,
        r.status,

        rt.room_type_id,
        rt.type_name,
        rt.price,
        rt.max_people

      FROM rooms r

      INNER JOIN room_types rt

      ON r.room_type_id=rt.room_type_id

      WHERE r.room_id='$id'

      LIMIT 1";

$query=mysqli_query($conn,$sql);

$row=mysqli_fetch_assoc($query);

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

success([

    "room"=>$row

],"Thêm phòng thành công.");

?>