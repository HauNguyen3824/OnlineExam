<?php
$servername = "localhost"; // Thay đổi nếu cần
$username = "root"; // Thay đổi theo người dùng của bạn
$password = ""; // Thay đổi nếu có mật khẩu
$dbname = "exam_management";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
