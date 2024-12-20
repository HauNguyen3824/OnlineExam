<?php
session_start();
require '../controller/db_exam_conn.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: ../public/dangnhap.html');
    exit();
}

$username = $_SESSION['username']; // Giả sử user_id lưu trong session

// Lấy danh sách các đề thi nháp
$sql = "SELECT * FROM exams WHERE username = ? AND status = 'draft'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
}

$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$sql1 = "SELECT * FROM exams WHERE username = ? AND status = 'published'";
$stmt1 = $conn->prepare($sql1);
if (!$stmt1) {
    die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
}
$stmt1->bind_param('s', $username);
$stmt1->execute();
$result1 = $stmt1->get_result();
// Xử lý khi người dùng nhấn nút hoàn chỉnh
if (isset($_POST['publish'])) {
    $examCode = $_POST['exam_code'];
    $updateSql = "UPDATE exams SET status = 'published' WHERE exam_code = ?";
    $updateStmt = $conn->prepare($updateSql);
    
    if ($updateStmt) {
        $updateStmt->bind_param('s', $examCode);
        $updateStmt->execute();
        $updateStmt->close();
        header("Location: quanlyde.php"); // Làm mới danh sách
        exit();
    } else {
        echo "Lỗi khi hoàn chỉnh đề thi: " . $conn->error;
    }
}
if (isset($_POST['delete'])) {
    $examCode = $_POST['exam_code'];
    $examId = $_POST['exam_id'];
    $questionId = [];
    $sqlQuestion = "SELECT idquestion FROM questions WHERE exam_id = ?";
    $stmtQuestion = $conn -> prepare($sqlQuestion);
    $stmtQuestion -> bind_param('i', $examId);
    $stmtQuestion -> execute();
    $questionResult = $stmtQuestion -> get_result();
    while ($question_id = $questionResult -> fetch_assoc()) $questionId[] = $question_id;
    foreach($questionId as $question) {
        $sqlChoiceDelete = "DELETE FROM choices WHERE question_id = ?";
        $stmtChoiceDelete = $conn -> prepare($sqlChoiceDelete);
        $stmtChoiceDelete -> bind_param('i', $question['idquestion']);
        $stmtChoiceDelete -> execute();
    }
    $sqlQuestionDelete = "DELETE FROM questions WHERE exam_id = ?";
    $stmtQuestionDelete = $conn -> prepare($sqlQuestionDelete);
    $stmtQuestionDelete -> bind_param('i', $examId);
    if ($stmtQuestionDelete -> execute()) {
    $sqlDelete = "DELETE FROM exams WHERE exam_code = ?";
    $stmtDelete = $conn -> prepare($sqlDelete);
    $stmtDelete -> bind_param('s', $examCode);
    }
    if ($stmtDelete -> execute()) echo "<script>alert('Xóa đề thành công'); window.location.href='quanlyde.php';</script>";
    
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đề thi</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Danh sách đề thi nháp</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã đề</th>
                    <th>Tên đề thi</th>
                    <th>Số lượng câu hỏi</th>
                    <th>Thời gian bắt đầu</th>
                    <th>Thời gian kết thúc</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['exam_code']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['num_questions']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_datetime']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_datetime']); ?></td>
                        <td>
                            <form method="post" action="../controller/suade.php" style="display:inline;">
                                <input type="hidden" name="exam_code" value="<?php echo htmlspecialchars($row['exam_code']); ?>">
                                <button type = "submit" name = "update" class ="btn btn-primary">Chỉnh sửa</button>
                            </form>
                            <form method="post" action="" style="display:inline;">
                                <input type="hidden" name="exam_code" value="<?php echo htmlspecialchars($row['exam_code']); ?>">
                                <button type="submit" name="publish" class="btn btn-success">Hoàn chỉnh</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <div class="container mt-5">
        <h2>Danh sách đề thi chính thức</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã đề</th>
                    <th>Tên đề thi</th>
                    <th>Số lượng câu hỏi</th>
                    <th>Thời gian bắt đầu</th>
                    <th>Thời gian kết thúc</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result1->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['exam_code']); ?></td>
                        <td><?php echo htmlspecialchars($row['exam_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['num_questions']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_datetime']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_datetime']); ?></td>
                        <td>
                            <form method="post" action="../public/trangchu.php" style="display:inline;">
                                <button type = "submit" name = "update" class ="btn btn-primary">Vào thi</button>
                            </form>
                            <form method="post" action="" style="display:inline;">
                                <input type = "hidden" name = "exam_id" value = "<?php echo htmlspecialchars($row['idexam']); ?>">
                                <input type="hidden" name="exam_code" value="<?php echo htmlspecialchars($row['exam_code']); ?>">
                                <button type="submit" name="delete" class="btn btn-danger">Kết thúc</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <center><a href="../public/trangchu.php" class="btn btn-danger">Thoát</a></center>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
