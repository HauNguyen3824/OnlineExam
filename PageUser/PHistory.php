<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PHistory.php
session_start();
include('../conn/conn_database.php');

// Lấy userId từ URL
$userId = $_GET['userId'] ?? null;
if (!$userId) {
    die("Không có người dùng nào được xác định.");
}

// Truy vấn bảng AddUser để lấy các AUId
$sql_auid = "SELECT AUId, ExamId FROM AddUser WHERE UserId = ?";
$stmt_auid = $conn->prepare($sql_auid);
$stmt_auid->bind_param("s", $userId);
$stmt_auid->execute();
$result_auid = $stmt_auid->get_result();

$auids = [];
$examIds = [];
while ($row = $result_auid->fetch_assoc()) {
    $auids[] = $row['AUId'];
    $examIds[$row['AUId']] = $row['ExamId'];
}
$stmt_auid->close();

if (empty($auids)) {
    $noResults = true;
} else {
    // Kiểm tra trong bảng results để lấy các AUId có kết quả
    $auid_string = "'" . implode("','", $auids) . "'";
    $sql_results = "SELECT AUId FROM results WHERE AUId IN ($auid_string)";
    $result_results = $conn->query($sql_results);

    $validAUIDs = [];
    while ($row = $result_results->fetch_assoc()) {
        $validAUIDs[] = $row['AUId'];
    }

    if (empty($validAUIDs)) {
        $noResults = true;
    } else {
        $noResults = false;
        // Lấy thông tin đề thi từ bảng Exams dựa trên examId
        $validExamIds = array_intersect_key($examIds, array_flip($validAUIDs));
        $examId_string = "'" . implode("','", $validExamIds) . "'";
        $sql_exams = "SELECT * FROM Exams WHERE ExamId IN ($examId_string)";
        $result_exams = $conn->query($sql_exams);

        if (!$result_exams) {
            die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử bài thi</title>
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
        <h1>Lịch sử bài thi</h1>
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
                <?php if ($noResults): ?>
                    <tr>
                        <td colspan="7" class="text-center">Người thi này chưa tham gia bài thi nào.</td>
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
                                <?php
                                // Lấy AUId từ bảng AddUser
                                $sql_auid = "SELECT AUId FROM AddUser WHERE ExamId = ? AND UserId = ?";
                                $stmt_auid = $conn->prepare($sql_auid);
                                $stmt_auid->bind_param("ss", $exam['ExamId'], $userId);
                                $stmt_auid->execute();
                                $result_auid = $stmt_auid->get_result();
                                $auid_row = $result_auid->fetch_assoc();
                                $stmt_auid->close();
                                $auid = $auid_row['AUId'];
                                ?>
                                <a href="PResultPage.php?examId=<?= htmlspecialchars($exam['ExamId']) ?>&userId=<?= htmlspecialchars($userId) ?>&auid=<?= htmlspecialchars($auid) ?>" class="btn btn-primary">Xem kết quả</a>
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