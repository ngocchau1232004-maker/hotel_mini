<?php

//* |=================================================================
//* |     HOTEL MINI API - Room Types
//* |  File: api/room_types/index.php
//* |  POST: http://localhost/hotel_mini/api/room_types/index.php
//* |=================================================================

require_once '../auth.php';

/*
|--------------------------------------------------------------------------
| GET hoặc POST
|--------------------------------------------------------------------------
*/

$id = 0;
$keyword = "";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $keyword = isset($_GET['keyword']) ? str($_GET['keyword']) : "";

}
elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = getJson();

    $id = isset($data['id']) ? intval($data['id']) : 0;
    $keyword = isset($data['keyword']) ? str($data['keyword']) : "";

}
else{

    error("Method không được hỗ trợ.",405);

}

/*
|--------------------------------------------------------------------------
| Lấy 1 loại phòng
|--------------------------------------------------------------------------
*/

if($id>0){

    $sql="SELECT *
          FROM room_types
          WHERE room_type_id='$id'
          LIMIT 1";

    $query=mysqli_query($conn,$sql);

    if(!$query){
        error(mysqli_error($conn),500);
    }

    if(mysqli_num_rows($query)==0){
        error("Không tìm thấy loại phòng.",404);
    }

    $row=mysqli_fetch_assoc($query);

    success($row,"Chi tiết loại phòng");

}

/*
|--------------------------------------------------------------------------
| Danh sách
|--------------------------------------------------------------------------
*/

$sql="SELECT *
      FROM room_types
      WHERE 1=1";

if($keyword!=""){

    $sql.=" AND (
                type_name LIKE '%$keyword%'
                OR description LIKE '%$keyword%'
            )";

}

$sql.=" ORDER BY room_type_id DESC";

$query=mysqli_query($conn,$sql);

if(!$query){
    error(mysqli_error($conn),500);
}

$list=[];

while($row=mysqli_fetch_assoc($query)){

    $list[]=[

        "room_type_id"=>(int)$row['room_type_id'],
        "type_name"=>$row['type_name'],
        "price"=>(int)$row['price'],
        "max_people"=>(int)$row['max_people'],
        "description"=>$row['description']

    ];

}

success([

    "total"=>count($list),
    "room_types"=>$list

],"Danh sách loại phòng");

?>