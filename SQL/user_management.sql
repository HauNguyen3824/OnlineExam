CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

-- Tạo bảng users để chứa thông tin người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,        -- Khóa chính tự động tăng
    username VARCHAR(50) NOT NULL UNIQUE,     -- Tên đăng nhập, phải là duy nhất
    password VARCHAR(255) NOT NULL,           -- Mật khẩu đã được mã hóa
    email VARCHAR(100) NOT NULL UNIQUE,       -- Email, phải là duy nhất
    fullname VARCHAR(100),                    -- Họ và tên người dùng
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Thời gian tạo tài khoản
);
