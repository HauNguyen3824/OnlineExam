<?php
session_start();
require '../controller/db_exam_conn.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: dangnhap.html');
    exit();
}

// Truy vấn danh sách lịch sử làm bài của người dùng
$user_id = $_SESSION['user_id']; // Giả sử user_id được lưu trong session khi người dùng đăng nhập
$history_query = "SELECT * FROM exam_results WHERE user_id = '$user_id'"; // Thay đổi tên bảng và điều kiện nếu cần
$history_result = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử làm bài</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        #header {
            margin-bottom: 20px;
        }
        .history-title {
            margin: 20px 0;
            text-align: center;
        }
        .history-table {
            margin: 0 auto;
            width: 80%;
        }
        .view-button {
            margin-left: 10px;
        }
    </style>
</head>
<body>
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
                    <li class="nav-item"><a class="nav-link" href="../controllrer/taode.php">Tạo đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="../controller/nhapma.php">Nhập mã</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/quanlyde.php">Quản lý đề</a></li>
                    <li class="nav-item"><a class="nav-link" href="../model/thongtincanhan.php">Thông tin cá nhân</a></li>
                    <li class="nav-item"><a class="nav-link" href="lichsu.php">Lịch sử làm bài</a></li>
                    <li class="nav-item"><a class="nav-link" href="../public/dangxuat.php">Đăng xuất</a></li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="container">
        <h1 class="history-title">Lịch sử làm bài</h1>
        <table class="table table-bordered history-table">
            <thead>
                <tr>
                    <th>Tên bài làm</th>
                    <th>Thời gian làm</th>
                    <th>Câu làm đúng / tổng số câu</th>
                    <th>Thống kê</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($history_result) > 0) {
                    while ($row = mysqli_fetch_assoc($history_result)) {
                        // Giả sử có các trường: id, title, time_taken, correct_answers, total_questions trong bảng
                        echo "<tr>
                                <td>{$row['title']}</td>
                                <td>{$row['time_taken']}</td>
                                <td>{$row['correct_answers']}/{$row['total_questions']}</td>
                                <td><button class='btn btn-primary view-button' onclick='viewTest({$row['id']})'>Xem</button></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>Chưa có lịch sử làm bài.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function viewTest(testId) {
            // Chuyển đến trang thongkeuser.php với ID bài kiểm tra tương ứng
            window.location.href = 'thongkeuser.php?id=' + testId;
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
