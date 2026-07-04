<?php

//* |===============================================================================
//* | HOTEL MINI API Delete Room Type
//* | File: api/room_types/delete.php
//* | GET: http://localhost/hotel_mini/api/room_types/delete.php?id=4(id muốn xóa)
//* |===============================================================================

require_once '../auth.php';

/*
|--------------------------------------------------------------------------
| GET hoặc DELETE
|--------------------------------------------------------------------------
*/

$id = 0;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

}
elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    parse_str(file_get_contents("php://input"), $data);

    $id = isset($data['id']) ? intval($data['id']) : 0;

}
else{

    error("Method không được hỗ trợ.",405);

}

/*
|--------------------------------------------------------------------------
| Validate
|--------------------------------------------------------------------------
*/

if($id<=0){
    error("ID loại phòng không hợp lệ.");
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

if(!$query){
    error(mysqli_error($conn),500);
}

if(mysqli_num_rows($query)==0){
    error("Không tìm thấy loại phòng.");
}

$row = mysqli_fetch_assoc($query);

/*
|--------------------------------------------------------------------------
| Kiểm tra phòng đang sử dụng loại phòng này
|--------------------------------------------------------------------------
*/

$sql = "SELECT room_id
        FROM rooms
        WHERE room_type_id='$id'
        LIMIT 1";

$query = mysqli_query($conn,$sql);

if(mysqli_num_rows($query)>0){

    error("Không thể xóa vì vẫn còn phòng thuộc loại phòng này.");

}

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

$sql = "DELETE
        FROM room_types
        WHERE room_type_id='$id'";

if(!mysqli_query($conn,$sql)){
    error(mysqli_error($conn),500);
}

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

success([

    "room_type_id"=>$id,
    "type_name"=>$row['type_name']

],"Xóa loại phòng thành công.");

?>