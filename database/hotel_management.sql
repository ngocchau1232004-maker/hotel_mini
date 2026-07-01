CREATE DATABASE hotel_management;
USE hotel_management;


-- roles (vai trò)
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL
);

INSERT INTO roles(role_name)
    VALUES  
        ('Admin'),
        ('Nhân viên');


-- users (tài khoản)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    email VARCHAR(100),
    role_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id) 
    REFERENCES roles(role_id)
);

INSERT INTO users(full_name, username, password, phone, email, role_id)
    VALUES('Administrator','admin','e10adc3949ba59abbe56e057f20f883e',NULL,NULL,1),
          ('Nguyễn Văn A','nhanvien1','e10adc3949ba59abbe56e057f20f883e','0912345678','an@gmail.com',2),
          ('Trần Thanh B','nhanvien2','e10adc3949ba59abbe56e057f20f883e','0988777666','binh@gmail.com',2);


-- room_types (loại phòng)
CREATE TABLE room_types (
    room_type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(100) NOT NULL,
    price INT NOT NULL COMMENT 'Giá phòng VNĐ',
    max_people INT NOT NULL,
    description TEXT
);

INSERT INTO room_types(type_name, price, max_people, description)
    VALUES
        ('Phòng Đơn', 150000, 2, 'Phòng dành cho 1-2 khách'),
        ('Phòng Đôi', 250000, 4, 'Phòng dành cho gia đình'),
        ('Phòng VIP', 500000, 4, 'Phòng cao cấp');



-- rooms (phòng)
CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    room_type_id INT NOT NULL,
        status ENUM(
            'Trống', 
            'Đã đặt', 
            'Đang thuê', 
            'Bảo trì'
        ) DEFAULT 'Trống',

    FOREIGN KEY (room_type_id) 
    REFERENCES room_types(room_type_id)
);

INSERT INTO rooms(room_number, room_type_id, status)
    VALUES
        ('A101', 1, 'Đang thuê'),
        ('A102', 1, 'Trống'),
        ('A103', 1, 'Trống'),
        ('B201', 2, 'Đã đặt'),
        ('B202', 2, 'Trống'),
        ('B203', 2, 'Trống'),
        ('VIP501', 3, 'Trống'),
        ('VIP502', 3, 'Đã đặt');



-- customers (khách hàng)
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    gender ENUM('Nam', 'Nữ', 'Khác'),
    phone VARCHAR(15),
    email VARCHAR(100),
    id_card VARCHAR(20),
    address TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO customers(full_name,gender,phone,email,id_card,address)
    VALUES 
        ('Nguyễn Văn Minh','Nam','0901111111','minh@gmail.com','079123456789','Cần Thơ'),
        ('Trần Thị Lan','Nữ','0902222222','lan@gmail.com','079987654321','Vĩnh Long'),
        ('Lê Quốc Huy','Nam','0903333333','huy@gmail.com','079456123789','An Giang'),
        ('Phạm Thu Hà','Nữ','0904444444','ha@gmail.com','079111222333','Sóc Trăng'),
        ('Đỗ Thanh Tùng','Nam','0905555555','tung@gmail.com','079555666777','Kiên Giang');


-- bookings (đặt phòng)
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,

    booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,

    actual_check_in DATETIME NULL,
    actual_check_out DATETIME NULL,

    status ENUM(
        'Đã đặt',
        'Đang thuê',
        'Đã trả phòng',
        'Đã hủy'
    ) DEFAULT 'Đã đặt',

    total_amount INT DEFAULT 0, note TEXT,

    FOREIGN KEY (customer_id)
    REFERENCES customers(customer_id)
);

INSERT INTO bookings(customer_id, check_in_date, check_out_date,
                    actual_check_in, actual_check_out, status,total_amount,note)
    VALUES
    (1,'2026-07-01','2026-07-03','2026-07-01 13:20:00',NULL,'Đang thuê',370000,'Khách ở 2 ngày'),
    (2,'2026-07-05','2026-07-06',NULL,NULL,'Đã đặt',250000,'Đặt online'),
    (3,'2026-06-25','2026-06-27','2026-06-25 14:00:00','2026-06-27 11:20:00','Đã trả phòng',650000,'Đã thanh toán'),
    (4,'2026-07-02','2026-07-04',NULL,NULL,'Đã đặt',500000,'Khách gọi điện'),
    (5,'2026-06-20','2026-06-22',NULL,NULL,'Đã hủy',0,'Khách hủy');



-- booking_details (chi tiết đặt phòng)
CREATE TABLE booking_details (
    booking_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    room_id INT NOT NULL,
    price INT NOT NULL,

    FOREIGN KEY (booking_id)
    REFERENCES bookings(booking_id)
    ON DELETE CASCADE,

    FOREIGN KEY (room_id)
    REFERENCES rooms(room_id)
);
 
INSERT INTO booking_details(booking_id,room_id,price)
    VALUES
        (1,1,150000),
        (2,4,250000),
        (3,7,500000),
        (4,8,500000);


-- services (dịch vụ)
CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    price INT NOT NULL COMMENT 'Giá dịch vụ VNĐ',
    description TEXT
);

INSERT INTO services(service_name,price,description)
    VALUES
        ('Nước suối',10000,'500ml'),
        ('Mì ly',25000,'Mì ăn liền'),
        ('Giặt quần áo',50000,'Theo kg'),
        ('Thuê xe máy',150000,'1 ngày'),
        ('Ăn sáng',50000,'Buffet');


-- service_usage (sử dụng dịch vụ)
CREATE TABLE service_usage (
    usage_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    service_id INT NOT NULL,
    quantity INT DEFAULT 1,
    usage_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (booking_id)
    REFERENCES bookings(booking_id),

    FOREIGN KEY (service_id)
    REFERENCES services(service_id)
);

INSERT INTO service_usage(booking_id,service_id,quantity)
    VALUES
        (1,1,4),
        (1,5,2),
        (1,3,1),
        (3,2,3),
        (3,4,1);

-- invoices (hóa đơn)
CREATE TABLE invoices (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    invoice_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    room_total INT DEFAULT 0,
    service_total INT DEFAULT 0,
    total_amount INT DEFAULT 0,

    FOREIGN KEY (booking_id)
    REFERENCES bookings(booking_id)
);

INSERT INTO invoices(booking_id,room_total,service_total,total_amount)
    VALUES (3,500000,150000,650000);

-- payments (thanh toán)
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,

    payment_method ENUM(
        'Tiền mặt',
        'Chuyển khoản',
        'Momo'
    ),

    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    amount INT NOT NULL,

    FOREIGN KEY (invoice_id)
    REFERENCES invoices(invoice_id)
);

INSERT INTO payments(invoice_id,payment_method,amount)
    VALUES(1,'Chuyển khoản',650000);