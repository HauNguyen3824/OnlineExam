<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PViewUserExams.php
session_start();
include('../conn/conn_database.php');

// Lấy userId từ URL
$userId = $_GET['userId'] ?? null;

if (!$userId) {
    die("Thiếu thông tin người dùng.");
}

// Truy vấn bảng adduser để lấy các auid có cùng userId
$sql_auid = "SELECT AUId FROM adduser WHERE userId = ?";
$stmt_auid = $conn->prepare($sql_auid);
if ($stmt_auid === false) {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
}
$stmt_auid->bind_param("s", $userId);
$stmt_auid->execute();
$result_auid = $stmt_auid->get_result();

if ($result_auid === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: {$conn->error}");
}

$auids = [];
while ($row = $result_auid->fetch_assoc()) {
    $auids[] = $row['AUId'];
}

// Truy vấn bảng results để lấy các auid có kết quả
$auids_with_results = [];
if (!empty($auids)) {
    $placeholders = implode(',', array_fill(0, count($auids), '?'));
    $sql_results = "SELECT AUId, Score, TimeStart, TimeSubmit FROM results WHERE AUId IN ($placeholders)";
    $stmt_results = $conn->prepare($sql_results);
    $stmt_results->bind_param(str_repeat('s', count($auids)), ...$auids);
    $stmt_results->execute();
    $result_results = $stmt_results->get_result();

    if ($result_results === false) {
        die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    while ($row = $result_results->fetch_assoc()) {
        $auids_with_results[$row['AUId']] = $row;
    }
}

// Truy vấn bảng adduser để lấy các examId từ các auid có kết quả
$exam_ids = [];
if (!empty($auids_with_results)) {
    $placeholders = implode(',', array_fill(0, count($auids_with_results), '?'));
    $sql_exam_ids = "SELECT DISTINCT examId, AUId FROM adduser WHERE AUId IN ($placeholders)";
    $stmt_exam_ids = $conn->prepare($sql_exam_ids);
    $auids_keys = array_keys($auids_with_results);
    $stmt_exam_ids->bind_param(str_repeat('s', count($auids_with_results)), ...$auids_keys);
    $stmt_exam_ids->execute();
    $result_exam_ids = $stmt_exam_ids->get_result();

    if ($result_exam_ids === false) {
        die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    while ($row = $result_exam_ids->fetch_assoc()) {
        $exam_ids[$row['examId']] = $row['AUId'];
    }
}

// Truy vấn bảng exams để lấy thông tin chi tiết của các bài thi
$exams = [];
if (!empty($exam_ids)) {
    $placeholders = implode(',', array_fill(0, count($exam_ids), '?'));
    $sql_exams = "SELECT examId, examTitle, duration, numOfQues, subject, difficult FROM exams WHERE examId IN ($placeholders)";
    $exam_ids_keys = array_keys($exam_ids);
    $stmt_exams = $conn->prepare($sql_exams);
    if ($stmt_exams === false) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    $stmt_exams->bind_param(str_repeat('s', count($exam_ids)), ...$exam_ids_keys);
    $exam_ids_keys = array_keys($exam_ids);
    $stmt_exams->bind_param(str_repeat('s', count($exam_ids)), ...$exam_ids_keys);
    $stmt_exams->execute();
    $result_exams = $stmt_exams->get_result();

    if ($result_exams === false) {
        die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    while ($row = $result_exams->fetch_assoc()) {
        $exams[$row['examId']] = $row;
    }
}

// Truy vấn bảng Users để lấy thông tin người dùng
$sql_user = "SELECT userId, fullName, email, phone, class, year FROM Users WHERE userId = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $userId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    die("Không tìm thấy thông tin người dùng.");
}

$user = $result_user->fetch_assoc();

// Xử lý yêu cầu làm lại bài thi
if (isset($_POST['reattempt'])) {
    $examId = $_POST['examId'];
    $userId = $_POST['userId'];

    // Truy vấn bảng adduser để lấy auid
    $sql_auid = "SELECT AUId FROM adduser WHERE examId = ? AND userId = ?";
    $stmt_auid = $conn->prepare($sql_auid);
    $stmt_auid->bind_param("ss", $examId, $userId);
    $stmt_auid->execute();
    $result_auid = $stmt_auid->get_result();

    if ($result_auid->num_rows === 0) {
        die("Không tìm thấy thông tin.");
    }

    $auid = $result_auid->fetch_assoc()['AUId'];

    // Xóa dữ liệu trong bảng results
    $sql_delete_results = "DELETE FROM results WHERE AUId = ?";
    $stmt_delete_results = $conn->prepare($sql_delete_results);
    $stmt_delete_results->bind_param("s", $auid);
    $stmt_delete_results->execute();

    // Xóa dữ liệu trong bảng useranswers
    $sql_delete_answers = "DELETE FROM useranswers WHERE AUId = ?";
    $stmt_delete_answers = $conn->prepare($sql_delete_answers);
    $stmt_delete_answers->bind_param("s", $auid);
    $stmt_delete_answers->execute();

    echo "<script>alert('Đã xóa dữ liệu thành công.'); window.location.href = 'PUserManagement.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem các bài thi đã tham gia</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        tr {
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Quản Lý Người Dùng</h1>
    </header>
    <div class="container mt-4">
    <div class="card mt-4">
            <div class="card-header">
                <h2>Thông tin người dùng</h2>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="userId">UserId</label>
                        <input type="text" class="form-control" id="userId" name="userId" value="<?= htmlspecialchars($user['userId']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fullName">Họ và tên</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" value="<?= htmlspecialchars($user['fullName']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="class">Lớp</label>
                        <input type="text" class="form-control" id="class" name="class" value="<?= htmlspecialchars($user['class']) ?>" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="year">Năm nhập học</label>
                        <input type="text" class="form-control" id="year" name="year" value="<?= htmlspecialchars($user['year']) ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
        <h1 class="text-center">Các bài thi đã tham gia</h1>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ExamId</th>
                    <th>Tên đề thi</th>
                    <th>Thời gian</th>
                    <th>Số lượng câu hỏi</th>
                    <th>Môn học</th>
                    <th>Độ khó</th>
                    <th>Điểm</th>
                    <th>Bắt đầu vào lúc</th>
                    <th>Nộp bài vào lúc</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($exams)): ?>
                    <?php foreach ($exams as $examId => $exam): ?>
                        <tr>
                            <td><?= htmlspecialchars($exam['examId']) ?></td>
                            <td><?= htmlspecialchars($exam['examTitle']) ?></td>
                            <td><?= htmlspecialchars($exam['duration']) ?></td>
                            <td><?= htmlspecialchars($exam['numOfQues']) ?></td>
                            <td><?= htmlspecialchars($exam['subject']) ?></td>
                            <td><?= htmlspecialchars($exam['difficult']) ?></td>
                            <td><?= htmlspecialchars($auids_with_results[$exam_ids[$examId]]['Score']) ?></td>
                            <td><?= htmlspecialchars($auids_with_results[$exam_ids[$examId]]['TimeStart']) ?></td>
                            <td><?= htmlspecialchars($auids_with_results[$exam_ids[$examId]]['TimeSubmit']) ?></td>
                            <td>
                                <a href="PViewExamDetails.php?examId=<?= htmlspecialchars($exam['examId']) ?>&userId=<?= htmlspecialchars($userId) ?>&auid=<?= htmlspecialchars($exam_ids[$examId]) ?>" class="btn btn-primary">Xem chi tiết</a>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="examId" value="<?= htmlspecialchars($exam['examId']) ?>">
                                    <input type="hidden" name="userId" value="<?= htmlspecialchars($userId) ?>">
                                    <input type="hidden" name="reattempt" value="1">
                                    <button type="button" class="btn btn-warning" onclick="confirmReattempt(this.form)">Làm lại</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Không có dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <button type="button" class="btn btn-secondary" onclick="confirmBack()">Quay lại</button>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmReattempt(form) {
            if (confirm('Bạn có chắc chắn muốn cho người dùng này làm lại không? Các dữ liệu cũ sẽ bị xóa.')) {
                form.submit();
            }
        }

        function confirmBack() {
            if (confirm('Quay lại sẽ không lưu dữ liệu hiện tại. Bạn có chắc chắn muốn quay lại?')) {
                window.location.href = 'PUserManagement.php';
            }
        }
    </script>
</body>
</html>
<?php
include('../template/Tfooter.php');
$stmt_auid->close();
$stmt_results->close();
$conn->close();
?>