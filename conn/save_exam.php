<?php
include('../conn/conn_database.php');
session_start();

function generateExamId($conn) {
    $base = "EX";
    
    do { // Vòng lặp để tạo ExamId cho đến khi tìm được ID chưa tồn tại
        $counter = rand(0, 999); // Sử dụng rand() để tạo số ngẫu nhiên 3 chữ số
        $examId = $base . str_pad($counter, 3, '0', STR_PAD_LEFT);

        // Kiểm tra xem ExamId đã tồn tại trong CSDL hay chưa
        $checkSql = "SELECT ExamId FROM Exams WHERE ExamId = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $examId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $exists = $checkResult->num_rows > 0; // Kiểm tra xem có kết quả trả về hay không

        $checkStmt->close();

    } while ($exists); // Tiếp tục vòng lặp nếu ExamId đã tồn tại

    return $examId;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testName = $_POST['test-name'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $difficulty = $_POST['difficulty'] ?? '';
    $numQuestions = (int)($_POST['num-questions'] ?? 0); // Cast to int, default to 0
    $duration = $_POST['duration'] ?? '';

    if (!preg_match("/^([0-1]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/", $duration)) {
        echo "error:Định dạng thời lượng không hợp lệ. Vui lòng sử dụng định dạng hh:mm:ss.";
        exit;
    }

    // Validate input (you can add more validation here)
    if (empty($testName) || empty($subject) || empty($difficulty) || $numQuestions <= 0 || empty($duration)) {
        echo "error:Vui lòng điền đầy đủ thông tin.";
        exit;
    }

    $examId = generateExamId($conn);

    $query = "INSERT INTO Exams (ExamId, ExamTitle, Duration, NumOfQues, Subject, Difficult) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo "error:Lỗi prepare: " . $conn->error;
        exit;
    }

    $stmt->bind_param('sssiss', $examId, $testName, $duration, $numQuestions, $subject, $difficulty);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Đề thi đã được lưu thành công!";
        header("Location: ../PageAdmin/PExamManagement.php"); // Redirect
        exit;
    } else {
        echo "error:Lỗi execute: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
} else {
    echo "error:Invalid request method.";
}
?>