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
    <?php include 'template/Tmenubar.php'; ?>

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
