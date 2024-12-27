<?php
include('conn/conn_database.php');
session_start();

/**
 * Hàm tìm và cập nhật thông tin bài thi trong cơ sở dữ liệu.
 */
function find_and_update_exam_content($examId, $examTitle, $examCode, $duration, $mixQuestions, $numberOfQuestion, $examPassword) {
    global $conn;

    // Truy vấn tìm kiếm thông tin bài thi
    $query = "SELECT * FROM Exams WHERE ExamId = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Lỗi chuẩn bị câu truy vấn: " . $conn->error);
    }

    // Liên kết tham số
    $stmt->bind_param('s', $examId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu bài thi tồn tại
    if ($result->num_rows > 0) {
        // Cập nhật thông tin bài thi
        $updateQuery = "UPDATE Exams SET ExamTitle = ?, ExamCode = ?, Duration = ?, MixQuestions = ?, NumberOfQuestion = ?, ExamPassword = ? WHERE ExamId = ?";
        $stmtUpdate = $conn->prepare($updateQuery);

        if (!$stmtUpdate) {
            die("Lỗi chuẩn bị câu truy vấn cập nhật: " . $conn->error);
        }

        // Liên kết các tham số vào câu truy vấn cập nhật
        $stmtUpdate->bind_param('sssssss', $examTitle, $examCode, $duration, $mixQuestions, $numberOfQuestion, $examPassword, $examId);

        // Thực thi câu truy vấn
        if ($stmtUpdate->execute()) {
            // Điều hướng tới PMenu.php sau khi cập nhật thành công
            echo "<script>
                    alert('Cập nhật thông tin bài thi thành công!');
                    window.location.href = 'PMenu.php';
                  </script>";
        } else {
            // Hiển thị thông báo lỗi khi không cập nhật thành công
            echo "<script>
                    alert('Lỗi khi cập nhật thông tin bài thi!');
                  </script>";
        }

        // Đóng statement cập nhật
        $stmtUpdate->close();
    } else {
        // Hiển thị thông báo nếu không tìm thấy bài thi
        echo "<script>
                alert('Không tìm thấy bài thi với ExamId: " . htmlspecialchars($examId) . "');
              </script>";
    }

    // Đóng statement tìm kiếm
    $stmt->close();
}

/**
 * Xử lý form khi được gửi bằng phương thức POST.
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy giá trị từ form
    $examId = isset($_POST['examId']) ? trim($_POST['examId']) : '';
    $examTitle = isset($_POST['examTitle']) ? trim($_POST['examTitle']) : '';
    $examCode = isset($_POST['examCode']) ? trim($_POST['examCode']) : '';
    $duration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
    $mixQuestions = isset($_POST['mixQuestions']) ? trim($_POST['mixQuestions']) : '';
    $numberOfQuestion = isset($_POST['numberOfQuestion']) ? trim($_POST['numberOfQuestion']) : '';
    $examPassword = isset($_POST['examPassword']) ? trim($_POST['examPassword']) : '';

    // Kiểm tra nếu đủ dữ liệu đầu vào
    if (!empty($examId) && !empty($examTitle) && !empty($examCode) && !empty($duration) && !empty($mixQuestions) && !empty($numberOfQuestion) && !empty($examPassword)) {
        // Gọi hàm tìm và cập nhật thông tin bài thi
        find_and_update_exam_content($examId, $examTitle, $examCode, $duration, $mixQuestions, $numberOfQuestion, $examPassword);
    } else {
        // Hiển thị thông báo khi thiếu dữ liệu
        echo "<script>
                alert('Vui lòng nhập đầy đủ thông tin!');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Vui lòng truy cập thông qua form hợp lệ.');
            window.history.back();
          </script>";
}
?>
