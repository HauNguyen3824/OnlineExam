<?php
include('conn/conn_database.php');
session_start();

// Hàm tạo ExamId tự động
function generateExamId($conn) {
    $sql = "SELECT MAX(CAST(SUBSTRING(ExamId, 4) AS UNSIGNED)) AS MaxId FROM Exams";
    $result = $conn->query($sql);
    $newId = 1;

    if ($result && $row = $result->fetch_assoc()) {
        $maxId = $row['MaxId'];
        if ($maxId !== null) {
            $newId = $maxId + 1;
        }
    }
    return "EX-" . str_pad($newId, 3, "0", STR_PAD_LEFT);
}

// Hàm tạo ExamCode ngẫu nhiên
function generateExamCode() {
    return strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6));
}

$examId = generateExamId($conn); // Sinh ExamId mới

$_SESSION['exam_id'] = $examId; // Lưu vào session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $examTitle = $_POST["examTitle"] ?? "";
    $examCode = $_POST["examCode"] ?? "";

    if (empty($examCode)) {
        $examCode = generateExamCode(); // Tạo ExamCode nếu không có
    }

    if (!empty($examTitle) && !empty($examCode)) {
        // Kiểm tra trùng ExamCode
        $stmtCheckCode = $conn->prepare("SELECT COUNT(*) AS count FROM Exams WHERE ExamCode = ?");
        $stmtCheckCode->bind_param("s", $examCode);
        $stmtCheckCode->execute();
        $resultCheckCode = $stmtCheckCode->get_result();
        $rowCheckCode = $resultCheckCode->fetch_assoc();

        if ($rowCheckCode['count'] > 0) {
            $errorMessage = "ExamCode đã tồn tại. Gợi ý: " . generateExamCode();
        } else {
            // Kiểm tra trùng tên bài thi
            $stmtCheckTitle = $conn->prepare("SELECT COUNT(*) AS count FROM Exams WHERE ExamTitle = ?");
            $stmtCheckTitle->bind_param("s", $examTitle);
            $stmtCheckTitle->execute();
            $resultCheckTitle = $stmtCheckTitle->get_result();
            $rowCheckTitle = $resultCheckTitle->fetch_assoc();

            if ($rowCheckTitle['count'] > 0) {
                $errorMessage = "Tên bài thi đã tồn tại. Vui lòng chọn tên khác.";
            } else {
                // Thêm bài thi mới vào cơ sở dữ liệu
                $stmt = $conn->prepare("INSERT INTO Exams (ExamId, ExamTitle, ExamCode) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $examId, $examTitle, $examCode);

                if ($stmt->execute()) {
                    // Điều hướng tới trang tạo đề
                    header("Location: PCreateExamContent.php");
                    exit();
                } else {
                    $errorMessage = "Lỗi khi thêm bài thi: " . $conn->error;
                }
                $stmt->close();
            }
            $stmtCheckTitle->close();
        }
        $stmtCheckCode->close();
    } else {
        $errorMessage = "Tên bài thi và ExamCode không được để trống.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khởi tạo ExamId</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form class="border p-4 bg-light" method="post" action="">
                    <h2 class="text-center">Khởi tạo ExamId</h2>

                    <!-- Hiển thị thông báo thành công hoặc lỗi -->
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>

                    <!-- Trường nhập tên bài thi -->
                    <div class="form-group">
                        <label for="examTitle">Tên bài thi</label>
                        <input type="text" class="form-control" id="examTitle" name="examTitle" placeholder="Nhập tên bài thi" required>
                    </div>

                    <!-- Trường nhập ExamCode -->
                    <div class="form-group">
                        <label for="examCode">ExamCode</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="examCode" name="examCode" placeholder="Nhập ExamCode" maxlength="6" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary" onclick="generateRandomCode()">Random</button>
                            </div>
                        </div>
                    </div>

                    <!-- Nút lưu -->
                    <button type="submit" class="btn btn-primary btn-block">Lưu bài thi</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function generateRandomCode() {
            const code = Math.random().toString(36).substring(2, 8).toUpperCase();
            document.getElementById("examCode").value = code;
        }
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
