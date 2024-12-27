<?php
session_start(); // Bắt đầu phiên làm việc

require 'db_exam_conn.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra nếu có dữ liệu mã bài thi được gửi qua POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $examCode = $_POST['examCode'];

    // Truy vấn mã bài thi trong cơ sở dữ liệu
    $query = "SELECT * FROM exams WHERE exam_code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $examCode);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu mã bài thi hợp lệ
    if ($result->num_rows > 0) {
        // Lưu thông tin bài thi vào session
        $_SESSION['exam_code'] = $examCode;

        // Chuyển đến trang làm bài thi
        header('Location: thongtinde.php');
        exit();
    } else {
        $errorMessage = "Mã bài thi không hợp lệ, vui lòng thử lại!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập mã bài thi</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <!-- Thanh Header -->
    <div id="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="trangchu.php" id="logo"> <!-- Giả sử đây là trang chính -->
                <img src="https://upload.wikimedia.org/wikipedia/vi/thumb/9/9e/Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_Minh.svg/1200px-Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_M%C3%Ình.svg.png" alt="logo" width="100">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="trangchu.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="taode.php">Tạo đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="nhapma.php">Nhập mã</a></li>
                    <li class="nav-item"><a class="nav-link" href="quanlyde.php">Quản lý đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="thongtincanhan.php">Thông tin cá nhân</a></li>
                    <li class="nav-item"><a class="nav-link" href="lichsu.php">Lịch sử làm bài</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Container chính để nhập mã bài thi -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 50vh;">
        <div class="card shadow-sm" style="width: 22rem;">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">Nhập mã bài thi</h5>
                <form method="POST" id="examCodeForm">
                    <div class="form-group">
                        <label for="examCode">Mã bài thi</label>
                        <input type="text" class="form-control" id="examCode" name="examCode" placeholder="Nhập mã bài thi" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Truy cập bài thi</button>
                </form>
                <?php if (isset($errorMessage)): ?>
                    <div id="errorMessage" class="alert alert-danger mt-3" role="alert">
                        <?= $errorMessage; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
