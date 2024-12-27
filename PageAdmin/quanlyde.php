<?php
session_start();
require 'db_exam_conn.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: dangnhap.html');
    exit();
}

$userId = $_SESSION['user_id']; // Giả sử user_id lưu trong session

// Lấy danh sách các đề thi nháp
$sql = "SELECT * FROM exams WHERE user_id = ? AND status = 'draft'";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
}

$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

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
                            <a href="taode.php?exam_code=<?php echo urlencode($row['exam_code']); ?>" class="btn btn-primary">Chỉnh sửa</a>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
