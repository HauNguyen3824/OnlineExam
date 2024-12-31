<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PResultPage.php
session_start();
include('../conn/conn_database.php');

// Lấy examId, userId và auid từ URL
$examId = $_GET['examId'] ?? null;
$userId = $_GET['userId'] ?? null;
$auid = $_GET['auid'] ?? null;

if (!$examId || !$userId || !$auid) {
    die("Thiếu thông tin cần thiết.");
}

// Truy vấn thông tin đề thi
$sql_exam = "SELECT * FROM Exams WHERE ExamId = ?";
$stmt_exam = $conn->prepare($sql_exam);
$stmt_exam->bind_param("s", $examId);
$stmt_exam->execute();
$result_exam = $stmt_exam->get_result();
$exam = $result_exam->fetch_assoc();
$stmt_exam->close();

// Truy vấn thông tin người dùng
$sql_user = "SELECT * FROM Users WHERE UserId = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $userId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

// Truy vấn thông tin kết quả
$sql_result = "SELECT * FROM results WHERE AUId = ?";
$stmt_result = $conn->prepare($sql_result);
$stmt_result->bind_param("s", $auid);
$stmt_result->execute();
$result_result = $stmt_result->get_result();
$result = $result_result->fetch_assoc();
$stmt_result->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả bài thi</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .card-body p {
            margin-bottom: 0.5rem;
        }
        .container {
            max-width: 900px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Kết quả bài thi</h1>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        Thông tin đề thi
                    </div>
                    <div class="card-body">
                        <p><strong>Tên đề thi: </strong> <?= htmlspecialchars($exam['ExamTitle']) ?></p>
                        <p><strong>Thời gian:</strong> <?= htmlspecialchars($exam['Duration']) ?></p>
                        <p><strong>Số lượng câu hỏi: </strong> <?= htmlspecialchars($exam['NumOfQues']) ?></p>
                        <p><strong>Môn:</strong> <?= htmlspecialchars($exam['Subject']) ?></p>
                        <p><strong>Độ khó:</strong> <?= htmlspecialchars($exam['Difficult']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        Thông tin người dùng
                    </div>
                    <div class="card-body">
                        <p><strong>Họ và tên:</strong> <?= htmlspecialchars($user['FullName']) ?></p>
                        <p><strong>Lớp:</strong> <?= htmlspecialchars($user['Class']) ?></p>
                        <p><strong>Năm nhập học:</strong> <?= htmlspecialchars($user['Year']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                Thông tin kết quả
            </div>
            <div class="card-body">
                <p><strong>Điểm:</strong> <?= htmlspecialchars($result['Score']) ?></p>
                <p><strong>Bắt đầu làm bài vào:</strong> <?= htmlspecialchars($result['TimeStart']) ?></p>
                <p><strong>Nộp bài lúc: </strong> <?= htmlspecialchars($result['TimeSubmit']) ?></p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="PMenuUser.php?userId=<?= htmlspecialchars($userId) ?>" class="btn btn-primary">Xác nhận</a>
            <a href="PAnswerPage.php?examId=<?= htmlspecialchars($examId) ?>&userId=<?= htmlspecialchars($userId) ?>&auid=<?= htmlspecialchars($auid) ?>" class="btn btn-secondary">Xem kết quả</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
include('../template/Tfooter.php');
$conn->close();
?>