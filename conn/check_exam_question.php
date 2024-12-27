<?php
// filepath: /d:/xampp/htdocs/doan/php/conn/check_exam_question.php
include('../conn/conn_database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $examId = $_POST['examId'] ?? null;
    $questionIds = $_POST['questionIds'] ?? [];

    if (!$examId || empty($questionIds)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
        exit;
    }

    $existingQuestions = [];
    $newQuestions = [];

    foreach ($questionIds as $quesId) {
        $sql = "SELECT COUNT(*) as count FROM AddQues WHERE ExamId = ? AND QuesId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $examId, $quesId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $existingQuestions[] = $quesId;
        } else {
            $newQuestions[] = $quesId;
        }
    }

    echo json_encode(['status' => 'success', 'existingQuestions' => $existingQuestions, 'newQuestions' => $newQuestions]);
    $conn->close();
}
?>