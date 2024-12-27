<?php
// save_exam_questions.php
include('../conn/conn_database.php');

function generateAQId($conn) {
    $prefix = "AQ";
    do {
        $random_number = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // Random number
        $aqId = $prefix . $random_number;
        $sql = "SELECT AQId FROM AddQues WHERE AQId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $aqId);
        $stmt->execute();
        $stmt->store_result(); // fetch results
    } while ($stmt->num_rows > 0); // repeat random number gen till result return 0

    return $aqId;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $examId = $_POST['examId'] ?? null;
    $questionIds = $_POST['questionIds'] ?? [];

    if (!$examId || empty($questionIds)) {
        echo "error:Invalid data.";
        exit;
    }

    foreach ($questionIds as $quesId) {
        $aqId = generateAQId($conn); // generate unique id for insertion, use same database to access
        $sql = "INSERT INTO AddQues (AQId, ExamId, QuesId) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $aqId, $examId, $quesId);

        if (!$stmt->execute()) {
            if ($conn->errno === 1062) { // Check for duplicate key error
                echo "error:Câu hỏi đã tồn tại trong đề thi.";
            } else {
                echo "error:Lỗi khi thêm câu hỏi: " . $stmt->error; // Other errors
            }
            exit;
        }

        $stmt->close();
    }

    echo "success";
}
?>
