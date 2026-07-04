<?php

//* |==============================================================
//* |     HOTEL MINI API - Bookings API
//* |  File: api/bookings/index.php
//* |  URL: http://localhost/hotel_mini/api/bookings/index.php
//* |==============================================================


require_once '../auth.php';

/*
|--------------------------------------------------------------------------
| GET hoặc POST
|--------------------------------------------------------------------------
*/

$id = 0;
$keyword = "";

if($_SERVER['REQUEST_METHOD']=="GET"){

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $keyword = isset($_GET['keyword']) ? str($_GET['keyword']) : "";

}elseif($_SERVER['REQUEST_METHOD']=="POST"){

    $data = getJson();

    $id = isset($data['id']) ? intval($data['id']) : 0;
    $keyword = isset($data['keyword']) ? str($data['keyword']) : "";

}else{

    error("Method không được hỗ trợ.",405);

}

/*
|--------------------------------------------------------------------------
| Chi tiết Booking
|--------------------------------------------------------------------------
*/

if($id>0){

    $sql="SELECT

            b.*,

            c.full_name,
            c.phone,
            c.email,
            c.id_card,

            GROUP_CONCAT(r.room_number SEPARATOR ', ') AS rooms

        FROM bookings b

        JOIN customers c
            ON b.customer_id=c.customer_id

        LEFT JOIN booking_details bd
            ON b.booking_id=bd.booking_id

        LEFT JOIN rooms r
            ON bd.room_id=r.room_id

        WHERE b.booking_id='$id'

        GROUP BY b.booking_id";

    $query=mysqli_query($conn,$sql);

    if(mysqli_num_rows($query)==0){

        error("Không tìm thấy booking.",404);

    }

    success(mysqli_fetch_assoc($query),"Chi tiết booking");

}

/*
|--------------------------------------------------------------------------
| Danh sách Booking
|--------------------------------------------------------------------------
*/

$sql="SELECT

        b.booking_id,
        c.full_name,

        GROUP_CONCAT(r.room_number) AS rooms,

        b.check_in_date,
        b.check_out_date,

        b.status,
        b.total_amount

    FROM bookings b

    JOIN customers c
        ON b.customer_id=c.customer_id

    LEFT JOIN booking_details bd
        ON b.booking_id=bd.booking_id

    LEFT JOIN rooms r
        ON bd.room_id=r.room_id

    WHERE 1=1";

if($keyword!=""){

    $sql.=" AND (

            c.full_name LIKE '%$keyword%'

            OR r.room_number LIKE '%$keyword%'

            OR b.status LIKE '%$keyword%'

        )";

}

$sql.="

GROUP BY b.booking_id

ORDER BY b.booking_id DESC";

$query=mysqli_query($conn,$sql);

$list=[];

while($row=mysqli_fetch_assoc($query)){

    $list[]=[

        "booking_id"=>(int)$row['booking_id'],

        "customer"=>$row['full_name'],

        "rooms"=>$row['rooms'],

        "check_in_date"=>$row['check_in_date'],

        "check_out_date"=>$row['check_out_date'],

        "status"=>$row['status'],

        "total_amount"=>(int)$row['total_amount']

    ];

}

success([

    "total"=>count($list),

    "bookings"=>$list

],"Danh sách booking");