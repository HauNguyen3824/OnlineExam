<?php
// Thông tin kết nối cơ sở dữ liệu
$servername = "localhost";  // Địa chỉ máy chủ MySQL (thường là localhost)
$username = "root";         // Tên người dùng MySQL
$password = "";             // Mật khẩu MySQL (nếu có)
$dbname = "onlineexam";       // Tên cơ sở dữ liệu bạn muốn kết nối

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} else {
}
?>
