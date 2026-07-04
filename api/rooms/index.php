<?php

//* |=============================================================
//* |   HOTEL MINI API - INDEX Room 
//* | File: api/rooms/index.php
//* | POST: http://localhost/hotel_mini/api/rooms/index.php
//* |=============================================================


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

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = getJson();

    $id = isset($data['id']) ? intval($data['id']) : 0;
    $keyword = isset($data['keyword']) ? str($data['keyword']) : "";

} else {

    error("Method không được hỗ trợ.",405);

}

/*
|--------------------------------------------------------------------------
| Lấy 1 phòng
|--------------------------------------------------------------------------
*/

if($id > 0){

    $sql = "SELECT
                r.room_id,
                r.room_number,
                r.status,
                rt.room_type_id,
                rt.type_name,
                rt.price,
                rt.max_people
            FROM rooms r
            INNER JOIN room_types rt
                    ON r.room_type_id = rt.room_type_id
            WHERE r.room_id='$id'
            LIMIT 1";

    $query = mysqli_query($conn,$sql);

    if(mysqli_num_rows($query)==0){
        error("Không tìm thấy phòng.",404);
    }

    success(mysqli_fetch_assoc($query),"Chi tiết phòng");
}

/*
|--------------------------------------------------------------------------
| Danh sách
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            r.room_id,
            r.room_number,
            r.status,

            rt.room_type_id,
            rt.type_name,
            rt.price,
            rt.max_people

        FROM rooms r

        INNER JOIN room_types rt
            ON r.room_type_id = rt.room_type_id

        WHERE 1=1";

if($keyword!=""){

    $sql.=" AND (
            r.room_number LIKE '%$keyword%'
            OR rt.type_name LIKE '%$keyword%'
            OR r.status LIKE '%$keyword%'
        )";

}

$sql.=" ORDER BY r.room_id DESC";

$query=mysqli_query($conn,$sql);

$list=[];

while($row=mysqli_fetch_assoc($query)){

    $list[]=[

        "room_id"=>(int)$row['room_id'],
        "room_number"=>$row['room_number'],

        "room_type"=>[
            "room_type_id"=>(int)$row['room_type_id'],
            "type_name"=>$row['type_name'],
            "price"=>(int)$row['price'],
            "max_people"=>(int)$row['max_people']
        ],

        "status"=>$row['status']

    ];

}

success([

    "total"=>count($list),
    "rooms"=>$list

],"Danh sách phòng");

?>