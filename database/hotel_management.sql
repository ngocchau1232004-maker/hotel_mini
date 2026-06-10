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
    role_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id) 
    REFERENCES roles(role_id)
);

INSERT INTO users(full_name, username, password, phone, email, role_id)
    VALUES('Administrator','admin','e10adc3949ba59abbe56e057f20f883e',NULL,NULL,1);

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
    room_type_id INT,
    status ENUM(
        'Trống', 
        'Đã đặt', 
        'Đang thuê', 
        'Bảo trì'
    ) DEFAULT 'Trống',

    FOREIGN KEY (room_type_id) 
    REFERENCES room_types(room_type_id)
);



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


-- bookings (đặt phòng)
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    booking_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    check_in_date DATE,
    check_out_date DATE,

    status ENUM(
        'Chờ xác nhận',
        'Đã xác nhận',
        'Đang thuê',
        'Đã trả phòng',
        'Đã hủy'
    ) DEFAULT 'Chờ xác nhận',

    total_amount INT DEFAULT 0,

    FOREIGN KEY (customer_id)
    REFERENCES customers(customer_id)
);


-- booking_details (chi tiết đặt phòng)
CREATE TABLE booking_details (
    booking_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    room_id INT,
    price INT,

    FOREIGN KEY (booking_id)
    REFERENCES bookings(booking_id),

    FOREIGN KEY (room_id)
    REFERENCES rooms(room_id)
);


-- services (dịch vụ)
CREATE TABLE services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    price INT NOT NULL COMMENT 'Giá dịch vụ VNĐ',
    description TEXT
);


-- service_usage (sử dụng dịch vụ)
CREATE TABLE service_usage (
    usage_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    service_id INT,
    quantity INT DEFAULT 1,
    usage_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (booking_id)
    REFERENCES bookings(booking_id),

    FOREIGN KEY (service_id)
    REFERENCES services(service_id)
);


-- invoices (hóa đơn)
CREATE TABLE invoices (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    invoice_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    room_total INT,
    service_total INT,
    total_amount INT,

    FOREIGN KEY (booking_id)
    REFERENCES bookings(booking_id)
);


-- payments (thanh toán)
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT,

    payment_method ENUM(
        'Tiền mặt',
        'Chuyển khoản',
        'Momo'
    ),

    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    amount INT,

    FOREIGN KEY (invoice_id)
    REFERENCES invoices(invoice_id)
);