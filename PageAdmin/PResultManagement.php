<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PResultManagement.php
session_start();
include('../conn/conn_database.php');

// Truy vấn dữ liệu từ bảng exams
$sql_exams = "SELECT examId, examTitle, duration, numOfQues, subject, difficult FROM exams";
$result_exams = $conn->query($sql_exams);

if ($result_exams === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý kết quả đề thi</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        tr {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../template/Tmenubar.php'; ?>
    <header class="bg-primary text-white text-center py-3">
         <h1>Quản Lý Kết Quả</h1>
    </header>
    <div class="container mt-4">
        <table class="table table-bordered mt-4">
            <thead>
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
                <?php if ($result_exams->num_rows > 0): ?>
                    <?php while ($row = $result_exams->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['examId']) ?></td>
                            <td><?= htmlspecialchars($row['examTitle']) ?></td>
                            <td><?= htmlspecialchars($row['duration']) ?></td>
                            <td><?= htmlspecialchars($row['numOfQues']) ?></td>
                            <td><?= htmlspecialchars($row['subject']) ?></td>
                            <td><?= htmlspecialchars($row['difficult']) ?></td>
                            <td>
                                <a href="PViewResults.php?examId=<?= htmlspecialchars($row['examId']) ?>" class="btn btn-primary">Xem kết quả</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Không có dữ liệu</td>
                    </tr>
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