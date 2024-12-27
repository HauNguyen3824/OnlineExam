<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: dangnhap.html');
    exit();
}

// Kết nối với cơ sở dữ liệu
$servername = "localhost";
$username_db = "root";  // Username của database
$password_db = "";  // Password của database
$dbname = "user_management";  // Tên database

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy thông tin người dùng từ CSDL
$current_user = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username='$current_user'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $fullname = $row['fullname'];
    $password = $row['password'];
    $email = $row['email'];
} else {
    echo "Không tìm thấy người dùng.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Cá Nhân</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Thông tin cá nhân -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Thông Tin Cá Nhân</h2>

                <!-- Username -->
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" value="<?php echo $username; ?>" readonly>
                </div>

                <!-- Fullname -->
                <div class="form-group">
                    <label for="fullname">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="fullname" value="<?php echo $fullname; ?>" readonly>
                </div>

                <!-- Mật khẩu -->
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="text" class="form-control" id="password" value="dữ liệu đã được mã hóa" readonly>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" value="<?php echo $email; ?>" readonly>
                </div>

                <!-- Nút chỉnh sửa -->
                <div class="text-center">
                    <a href="edit.php" class="btn btn-primary">Chỉnh sửa</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
