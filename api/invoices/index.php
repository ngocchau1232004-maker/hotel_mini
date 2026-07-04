<?php
header("Content-Type: application/json");
include "../../config/database.php";

$sql = "SELECT
            i.invoice_id,
            i.invoice_date,
            i.room_total,
            i.service_total,
            i.total_amount,
            b.booking_id,
            c.full_name,
            GROUP_CONCAT(r.room_number SEPARATOR ', ') AS rooms,
            (
                SELECT COUNT(*)
                FROM payments p
                WHERE p.invoice_id=i.invoice_id
            ) AS paid
        FROM invoices i
        JOIN bookings b
            ON i.booking_id=b.booking_id
        JOIN customers c
            ON b.customer_id=c.customer_id
        LEFT JOIN booking_details bd
            ON b.booking_id=bd.booking_id
        LEFT JOIN rooms r
            ON bd.room_id=r.room_id
        GROUP BY i.invoice_id
        ORDER BY i.invoice_id DESC";

$result=mysqli_query($conn,$sql);

$data=[];

while($row=mysqli_fetch_assoc($result)){

    $row['payment_status']=$row['paid']>0?
        "Đã thanh toán":"Chưa thanh toán";

    $data[]=$row;
}

echo json_encode([
    "success"=>true,
    "total"=>count($data),
    "data"=>$data
],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);

?>