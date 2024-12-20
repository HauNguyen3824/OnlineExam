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

// Xử lý khi người dùng nhấn nút đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dangnhap'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra tên đăng nhập và mật khẩu không được để trống
    if (empty($username) || empty($password)) {
        echo "<script>alert('Vui lòng nhập tên đăng nhập và mật khẩu!');</script>";
    } else {
        // Chuẩn bị câu lệnh SQL
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Kiểm tra kết quả
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Xác thực mật khẩu
            if (password_verify($password, $user['password'])) {
                // Lưu thông tin vào session
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['user_id']; // Lưu user_id vào session

                echo "<script>alert('Đăng nhập thành công!'); window.location.href='trangchu.php';</script>";
            } else {
                echo "<script>alert('Mật khẩu không đúng!');</script>";
            }
        } else {
            echo "<script>alert('Tên đăng nhập không tồn tại!');</script>";
        }
        $stmt->close();
    }
}

$conn->close();
?>
