<?php

//* |===============================================================================
//* | HOTEL MINI API Delete Room Type
//* | File: api/rooms/delete.php
//* | GET: http://localhost/hotel_mini/api/rooms/delete.php?id=11(id muốn xóa)
//* |===============================================================================


require_once '../auth.php';

/*
|--------------------------------------------------------------------------
| GET hoặc DELETE
|--------------------------------------------------------------------------
*/

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
    error("ID phòng không hợp lệ.");
}

/*
|--------------------------------------------------------------------------
| Kiểm tra phòng tồn tại
|--------------------------------------------------------------------------
*/

$sql="SELECT *
      FROM rooms
      WHERE room_id='$id'
      LIMIT 1";

$query=mysqli_query($conn,$sql);

if(mysqli_num_rows($query)==0){
    error("Không tìm thấy phòng.");
}

$room=mysqli_fetch_assoc($query);

/*
|--------------------------------------------------------------------------
| Kiểm tra khóa ngoại
|--------------------------------------------------------------------------
*/

$sql="SELECT booking_detail_id
      FROM booking_details
      WHERE room_id='$id'
      LIMIT 1";

$query=mysqli_query($conn,$sql);

if(mysqli_num_rows($query)>0){

    error("Không thể xóa vì phòng đã được sử dụng trong phiếu đặt phòng.");

}

/*
|--------------------------------------------------------------------------
| Xóa
|--------------------------------------------------------------------------
*/

$sql="DELETE FROM rooms
      WHERE room_id='$id'";

if(!mysqli_query($conn,$sql)){

    error(mysqli_error($conn),500);

}

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

success([

    "deleted_room"=>[
        "room_id"=>$room['room_id'],
        "room_number"=>$room['room_number']
    ]

],"Xóa phòng thành công.");

?>