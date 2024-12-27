<?php
// filepath: /d:/xampp/htdocs/doan/php/conn/check_exam.php
include('../conn/conn_database.php');

$examId = $_POST['examId'];
$userId = $_POST['userId'];

// Lấy AUId từ bảng AddUser
$sql_auid = "SELECT AUId FROM AddUser WHERE ExamId = ? AND UserId = ?";
$stmt_auid = $conn->prepare($sql_auid);
$stmt_auid->bind_param("ss", $examId, $userId);
$stmt_auid->execute();
$result_auid = $stmt_auid->get_result();
$auid_row = $result_auid->fetch_assoc();
$stmt_auid->close();

if (!$auid_row) {
    echo 'error';
    exit;
}

$auid = $auid_row['AUId'];

// Kiểm tra xem người dùng đã làm bài thi này chưa
$sql_check_result = "SELECT resultId FROM results WHERE AUId = ?";
$stmt_check_result = $conn->prepare($sql_check_result);
$stmt_check_result->bind_param("s", $auid);
$stmt_check_result->execute();
$result_check_result = $stmt_check_result->get_result();
$stmt_check_result->close();

if ($result_check_result->num_rows > 0) {
    echo 'exists';
} else {
    echo 'allowed';
}
?>