<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: dangnhap.html');
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "user_management";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$current_user = $_SESSION['username'];
$currentPassword = $_POST['currentPassword'];
$newPassword = $_POST['newPassword'];
$confirmPassword = $_POST['confirmPassword'];

// Kiểm tra mật khẩu hiện tại
$sql = "SELECT password FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $current_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    // Xác minh mật khẩu hiện tại
    if (!password_verify($currentPassword, $hashed_password)) {
        echo "<script>alert('Mật khẩu hiện tại không chính xác!'); window.location.href='thongtincanhan.php';</script>";
        exit();
    }

    // Kiểm tra xem mật khẩu mới và xác nhận có khớp không
    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Mật khẩu mới và xác nhận mật khẩu không khớp!'); window.location.href='thongtincanhan.php';</script>";
        exit();
    }

    // Cập nhật mật khẩu mới
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $update_sql = "UPDATE users SET password = ? WHERE username = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ss', $newHashedPassword, $current_user);

    if ($update_stmt->execute()) {
        echo "<script>alert('Cập nhật mật khẩu thành công!'); window.location.href='thongtincanhan.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật mật khẩu!'); window.location.href='thongtincanhan.php';</script>";
    }

    $update_stmt->close();
} else {
    echo "Không tìm thấy người dùng.";
}

$stmt->close();
$conn->close();
?>
