<?php
// filepath: /d:/xampp/htdocs/doan/php/conn/submit_exam.php
session_start();
include('../conn/conn_database.php');

$examId = $_GET['examId'];
$userId = $_GET['userId'];
$answers = $_POST['answers'];
$timeStart = $_SESSION['timeStart'];
$timeSubmit = date('Y-m-d H:i:s');

// Hàm tạo ID ngẫu nhiên không trùng
function generateRandomId($length = 10) {
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

// Lấy AUId từ bảng AddUser
$sql_auid = "SELECT AUId FROM AddUser WHERE ExamId = ? AND UserId = ?";
$stmt_auid = $conn->prepare($sql_auid);
if ($stmt_auid === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt_auid->bind_param("ss", $examId, $userId);
$stmt_auid->execute();
$result_auid = $stmt_auid->get_result();
$auid = $result_auid->fetch_assoc()['AUId'];
$stmt_auid->close();

// Lưu đáp án của người dùng vào bảng useranswer
$score = 0;
foreach ($answers as $quesId => $userChoice) {
    $ansId = generateRandomId();

    // Truy vấn đáp án đúng từ bảng Questions
    $sql_correct = "SELECT Correct FROM Questions WHERE QuesId = ?";
    $stmt_correct = $conn->prepare($sql_correct);
    $stmt_correct->bind_param("s", $quesId);
    $stmt_correct->execute();
    $result_correct = $stmt_correct->get_result();
    $correctAnswer = $result_correct->fetch_assoc()['Correct'];
    $stmt_correct->close();

    // Kiểm tra đáp án đúng
    $isCorrect = ($userChoice == $correctAnswer) ? 1 : 0;
    if ($isCorrect) {
        $score++;
    }

    // Lưu vào bảng useranswers
    $sql_insert_answer = "INSERT INTO useranswers (AnsId, UserChoice, IsCorrect, QuesId, AUId) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert_answer = $conn->prepare($sql_insert_answer);
    if ($stmt_insert_answer === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt_insert_answer->bind_param("ssiss", $ansId, $userChoice, $isCorrect, $quesId, $auid);
    $stmt_insert_answer->execute();
    $stmt_insert_answer->close();
}

// Lưu kết quả vào bảng results
$resultId = generateRandomId();
$sql_insert_result = "INSERT INTO results (resultId, AUId, Score, TimeStart, TimeSubmit) VALUES (?, ?, ?, ?, ?)";
$stmt_insert_result = $conn->prepare($sql_insert_result);
$stmt_insert_result->bind_param("ssiss", $resultId, $auid, $score, $timeStart, $timeSubmit);
$stmt_insert_result->execute();
$stmt_insert_result->close();

$conn->close();

// Chuyển hướng về trang tạm thời để hiển thị kết quả
header("Location: ../PageUser/PResultPage.php?examId=$examId&userId=$userId&auid=$auid");
exit();
?>