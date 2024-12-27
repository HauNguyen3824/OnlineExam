<?php
// Bắt lỗi nếu có
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Lấy dữ liệu POST
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra xem dữ liệu có hợp lệ không
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

include('conn_database.php');

// Kiểm tra kết nối cơ sở dữ liệu
if ($conn->connect_error) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Duyệt qua tất cả câu hỏi trong dữ liệu gửi lên
foreach ($data as $question) {
    // Kiểm tra và chuẩn hóa dữ liệu
    $questionTitle = $conn->real_escape_string($question['QuestionTitle']);
    $answer1 = $conn->real_escape_string($question['Answer1']);
    $answer2 = $conn->real_escape_string($question['Answer2']);
    $answer3 = $conn->real_escape_string($question['Answer3']);
    $answer4 = $conn->real_escape_string($question['Answer4']);
    $correct = $conn->real_escape_string($question['Correct']);
    $mixAnswers = (int)$question['MixAnswers'];  // Đảm bảo MixAnswers là số
    $score = (int)$question['Score'];  // Đảm bảo Score là số
    $examId = $conn->real_escape_string($question['ExamId']);
    $questionId = $conn->real_escape_string($question['QuestionId']);

    // Kiểm tra các trường dữ liệu bắt buộc có tồn tại không
    if (empty($questionTitle) || empty($answer1) || empty($answer2) || empty($correct)) {
        echo json_encode(['status' => 'error', 'message' => 'Các trường dữ liệu bắt buộc không được để trống']);
        exit;
    }

    // Thực hiện câu lệnh INSERT
    $sql = "INSERT INTO Questions (QuestionId, QuestionTitle, Answer1, Answer2, Answer3, Answer4, Correct, MixAnswers, Score, ExamId)
            VALUES ('$questionId', '$questionTitle', '$answer1', '$answer2', '$answer3', '$answer4', '$correct', $mixAnswers, $score, '$examId')";

    if (!$conn->query($sql)) {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi lưu câu hỏi: ' . $conn->error]);
        exit;
    }
}

// Đóng kết nối
$conn->close();

// Trả về thông báo thành công
echo json_encode(['status' => 'success', 'message' => 'Dữ liệu câu hỏi đã được lưu thành công']);
?>
