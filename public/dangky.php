<?php
session_start();

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý khi người dùng nhấn nút đăng ký
if (isset($_POST['dangky'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mã hóa mật khẩu
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];

    // Kiểm tra các trường không được để trống
    if (empty($username) || empty($password) || empty($email) || empty($fullname)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!');</script>";
    } else {
        // Kiểm tra xem tên đăng nhập đã tồn tại chưa
        $check_sql = "SELECT * FROM users WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Tên đăng nhập đã tồn tại
            echo "<script>alert('Tên đăng nhập này đã tồn tại. Vui lòng chọn tên đăng nhập khác.');</script>";
        } else {
            // Chuẩn bị câu lệnh SQL để chèn người dùng mới
            $sql = "INSERT INTO users (username, password, email, fullname) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $password, $email, $fullname);

            // Thực thi và kiểm tra kết quả
            if ($stmt->execute()) {
                echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập lại dể truy cập vô hệ thống.'); window.location.href='dangnhap.html';</script>";
            } else {
                echo "<script>alert('Lỗi khi đăng ký!');</script>";
            }
            $stmt->close();
        }
        $check_stmt->close(); // Đóng statement kiểm tra
    }
}

$conn->close();
?>
