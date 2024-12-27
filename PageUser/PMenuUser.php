<?php
include('../conn/conn_save_session.php');
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
    <?php include('../template/TmenuUser.php'); ?>

    <!-- Tiêu đề -->
    <div class="hero">
        <div class="container">
            <h1 class="display-4">Đề Thi Trắc Nghiệm</h1>
            <p class="lead">Chào mừng bạn đến với ứng dụng làm đề thi trắc nghiệm dễ dàng và nhanh chóng!</p>
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
                        <p class="card-text">Làm đề thi nhanh gọn, trực quan và dễ dàng sử dụng.</p>
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
                        <p class="card-text">Cung cấp báo cáo chi tiết về kết quả làm bài của bạn.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php
    include('../template/Tfooter.php');
    ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
