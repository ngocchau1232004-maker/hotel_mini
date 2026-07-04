<?php
header("Content-Type: application/json");
include "../../config/database.php";

if(!isset($_GET['id'])){
    echo json_encode([
        "success"=>false,
        "message"=>"Thiếu id"
    ]);
    exit();
}

$id=intval($_GET['id']);

$sql="SELECT
        i.*,
        b.booking_id,
        b.booking_date,
        b.check_in_date,
        b.check_out_date,
        b.actual_check_in,
        b.actual_check_out,
        c.full_name,
        c.phone,
        c.email,
        c.id_card,
        c.address
    FROM invoices i
    JOIN bookings b
        ON i.booking_id=b.booking_id
    JOIN customers c
        ON b.customer_id=c.customer_id
    WHERE i.invoice_id='$id'";

$result=mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){

    echo json_encode([
        "success"=>false,
        "message"=>"Không tìm thấy hóa đơn"
    ]);

    exit();
}

$invoice=mysqli_fetch_assoc($result);

$rooms=[];

$q=mysqli_query($conn,"
SELECT
r.room_number,
rt.type_name,
bd.price
FROM booking_details bd
JOIN rooms r
ON bd.room_id=r.room_id
JOIN room_types rt
ON r.room_type_id=rt.room_type_id
WHERE bd.booking_id=".$invoice['booking_id']);

while($r=mysqli_fetch_assoc($q)){
    $rooms[]=$r;
}

$services=[];

$q=mysqli_query($conn,"
SELECT
s.service_name,
s.price,
su.quantity,
su.usage_date
FROM service_usage su
JOIN services s
ON su.service_id=s.service_id
WHERE su.booking_id=".$invoice['booking_id']);

while($r=mysqli_fetch_assoc($q)){
    $r['total']=$r['price']*$r['quantity'];
    $services[]=$r;
}

echo json_encode([
    "success"=>true,
    "invoice"=>$invoice,
    "rooms"=>$rooms,
    "services"=>$services
],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

?>