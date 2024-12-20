<?php
session_start();
include 'database_connection.php'; // Kết nối đến database

// Giả sử mã đề thi được lưu trong session
if (!isset($_SESSION['exam_code'])) {
    header("Location: nhapma.php"); // Chuyển về trang nhập mã nếu không có mã đề
    exit();
}

// Lấy mã đề từ session
$exam_code = $_SESSION['exam_code'];

// Truy vấn thông tin đề thi từ database
$query = "SELECT * FROM exam_management WHERE exam_code = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $exam_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $exam_info = $result->fetch_assoc();
} else {
    echo "Không tìm thấy đề thi.";
    exit();
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin đề thi</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <!-- Thanh Header -->
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
                    <li class="nav-item"><a class="nav-link" href="">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="">Tạo đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="">Nhập mã</a></li>
                    <li class="nav-item"><a class="nav-link" href="">Quản lý đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="">Thông tin cá nhân</a></li>
                    <li class="nav-item"><a class="nav-link" href="">Lịch sử làm bài</a></li>
                    <li class="nav-item"><a class="nav-link" href="">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- Thông tin đề thi -->
    <div class="container mt-4">
        <h1 class="text-center">Thông tin đề thi</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $exam_info['exam_title']; ?></h5>
                <p><strong>Thời gian thi:</strong> <?php echo $exam_info['duration']; ?> phút</p>
                <p><strong>Thời gian mở:</strong> <?php echo $exam_info['open_time']; ?></p>
                <p><strong>Thời gian đóng:</strong> <?php echo $exam_info['close_time']; ?></p>
                <p><strong>Số lượng câu hỏi:</strong> <?php echo $exam_info['question_count']; ?></p>
                <p><strong>Người tạo đề:</strong> <?php echo $exam_info['creator_name']; ?></p>
                <button class="btn btn-success" onclick="confirmStart()">Bắt đầu làm bài</button>
            </div>
        </div>
    </div>

    <script>
        function confirmStart() {
            if (confirm("Bạn đã sẵn sàng làm bài chưa?")) {
                // Chuyển đến trang làm bài (giả sử là trang 'start_exam.php')
                window.location.href = "start_exam.php";
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
