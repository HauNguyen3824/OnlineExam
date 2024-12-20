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

    <!-- Thanh header -->
    <div id="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="trangchu.php">
                <img src="https://upload.wikimedia.org/wikipedia/vi/thumb/9/9e/Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_Minh.svg/1200px-Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_Minh.svg.png" 
                alt="logo" width="100">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="../public/trangchu.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="../controller/taode.php">Tạo đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="../controller/nhapma.php">Nhập mã</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/quanlyde.php">Quản lý đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="thongtincanhan.php">Thông tin cá nhân</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/lichsu.php">Lịch sử làm bài</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/dangxuat.php">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Thông tin cá nhân -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Thông Tin Cá Nhân</h2>
                <p><b>Lưu ý: Bạn phải bấm nút chỉnh sửa thì mới sửa được thông tin cá nhân</b></p>
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
                    <a href="../public/edit.php" class="btn btn-primary">Chỉnh sửa</a>
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
