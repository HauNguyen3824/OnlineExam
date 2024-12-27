<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PExamView.php
session_start();
include('../conn/conn_database.php');

// Lấy userId từ URL
$userId = $_GET['userId'] ?? null;
if (!$userId) {
    // Nếu userId không tồn tại trong URL, chuyển hướng đến trang đăng nhập
    header('Location: ../php/PLogin.php');
    exit;
}

// Truy vấn bảng AddUsers để tìm các examId được gắn với userId
$sql_add_users = "SELECT ExamId FROM AddUser WHERE UserId = ?";
$stmt_add_users = $conn->prepare($sql_add_users);
if ($stmt_add_users) {
    $stmt_add_users->bind_param("s", $userId);
    $stmt_add_users->execute();
} else {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
}
$result_add_users = $stmt_add_users->get_result();

$examIds = [];
while ($row = $result_add_users->fetch_assoc()) {
    $examIds[] = $row['ExamId'];
}

$stmt_add_users->close();

if (empty($examIds)) {
    $noExams = true;
} else {
    $noExams = false;
    // Truy vấn bảng Exams để lấy thông tin đề thi
    $examId_string = "'" . implode("','", $examIds) . "'";
    $sql_exams = "SELECT * FROM Exams WHERE ExamId IN ($examId_string)";
    $result_exams = $conn->query($sql_exams);

    if (!$result_exams) {
        die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đề thi</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        tr {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../template/TmenuUser.php'; ?>
    <header class="bg-primary text-white text-center py-3">
        <h1>Danh sách đề thi</h1>
    </header>
    <div class="container mt-4">
        <table class="table table-bordered mt-4">
            <thead class="table-primary">
                <tr>
                    <th>ExamId</th>
                    <th>Tên đề thi</th>
                    <th>Thời gian</th>
                    <th>Số lượng câu hỏi</th>
                    <th>Môn</th>
                    <th>Độ khó</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($noExams): ?>
                    <tr>
                        <td colspan="7" class="text-center">Người dùng này chưa có đề thi nào.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($exam = $result_exams->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['ExamId']) ?></td>
                            <td><?= htmlspecialchars($exam['ExamTitle']) ?></td>
                            <td><?= htmlspecialchars($exam['Duration']) ?></td>
                            <td><?= htmlspecialchars($exam['NumOfQues']) ?></td>
                            <td><?= htmlspecialchars($exam['Subject']) ?></td>
                            <td><?= htmlspecialchars($exam['Difficult']) ?></td>
                            <td>
                                <a href="PExamDetail.php?examId=<?= htmlspecialchars($exam['ExamId']) ?>&userId=<?= htmlspecialchars($userId) ?>" class="btn btn-primary">Xem chi tiết</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
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