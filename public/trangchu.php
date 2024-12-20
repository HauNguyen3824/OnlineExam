<?php
session_start(); // Bắt đầu session

// Kiểm tra nếu người dùng đã đăng nhập, nếu không chuyển hướng đến trang đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: dangnhap.html"); // Chuyển đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Lấy thông tin người dùng từ session
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ - Tạo Đề Thi Trắc Nghiệm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .hero {
            background-color: #007bff;
            color: white;
            padding: 50px 0;
            text-align: center;
        }
        .feature {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div id="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#" id="logo">
                <img src="https://upload.wikimedia.org/wikipedia/vi/thumb/9/9e/Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_Minh.svg/1200px-Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_M%C3%Ình.svg.png" 
                alt="logo" width="100">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="trangchu.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="../controller/taode.php">Tạo đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="../controller/nhapma.php">Nhập mã</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/quanlyde.php">Quản lý đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="../model/thongtincanhan.php">Thông tin cá nhân</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/lichsu.php">Lịch sử làm bài</a></li>
                    <li class="nav-item"><a class="nav-link" href="dangxuat.php">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Tiêu đề -->
    <div class="hero">
        <div class="container">
            <h1 class="display-4">Tạo Đề Thi Trắc Nghiệm</h1>
            <p class="lead">Chào mừng bạn đến với ứng dụng tạo đề thi trắc nghiệm dễ dàng và nhanh chóng!</p>
            <p class="lead">Xin chào, <?php echo htmlspecialchars($username); ?>!</p> <!-- Hiển thị tên người dùng -->
        </div>
    </div>

    <!-- Các tính năng -->
    <div class="container feature">
        <h2 class="text-center">Các Tính Năng Nổi Bật</h2>
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Giao Diện Trực Quan</h5>
                        <p class="card-text">Tạo đề nhanh gọn, trực quan và dễ dàng sử dụng.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Trộn Câu Hỏi</h5>
                        <p class="card-text">Cho phép trộn ngẫu nhiên câu hỏi để mỗi lần làm bài là một trải nghiệm khác nhau.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Thống Kê Kết Quả</h5>
                        <p class="card-text">Cung cấp báo cáo chi tiết về kết quả làm bài của người dùng.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4">
        <p>&copy; Nhóm 6 - Website Tạo Đề Thi Trắc Nghiệm</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
