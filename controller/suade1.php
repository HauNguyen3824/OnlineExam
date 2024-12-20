<?php
session_start();
require 'db_exam_conn.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: dangnhap.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveDraft'])) {
        $examId = $_POST['examId'];
        $examCode = $_POST['examCode'];
        $examName = $_POST['examName'];
        $numQuestions = $_POST['numQuestions'];
        $password = $_POST['password'];
        $startDatetime = $_POST['startDatetime'];
        $endDatetime = $_POST['endDatetime'];
        $mixQuestions = isset($_POST['mixQuestions']) ? 1 : 0;

    // Thực hiện câu lệnh SQL để cập nhật bảng exams
    $sqlUpdate = "UPDATE exams SET exam_name=?, num_questions=?, password=?, start_datetime=?, end_datetime=?, mix_questions=? WHERE exam_code=?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param('sisssis', $examName, $numQuestions, $password, $startDatetime, $endDatetime, $mixQuestions, $examCode);
    
    if ($stmtUpdate->execute()) {
        for ($i = 1; $i <= $numQuestions; $i++) {
            $questionId = $_POST["questionId{$i}"];
            $questionText = $_POST["formName{$i}"];
            $numChoices = $_POST["numChoice{$i}"];
            $correctChoice = $_POST["correctChoice{$i}"];
            $score = $_POST["score{$i}"];
            $mixChoices = isset($_POST["mixChoiceAns{$i}"]) ? 1 : 0;
            $sqlQuestionUpdate = "UPDATE questions SET question_text=?, correct_answer=?, score=?, mix_answers=? WHERE exam_id=? AND idquestion = ?";
            $stmtQuestionUpdate = $conn->prepare($sqlQuestionUpdate);
            $stmtQuestionUpdate -> bind_param('ssiiii', $questionText, $correctChoice, $score, $mixChoices, $examId, $questionId);
            if ($stmtQuestionUpdate -> execute()) {
                for ($j = 1; $j <= $numChoices; $j++) {
                    $choiceId = $_POST["choice_id{$i}_{$j}"];
                    $choiceText = $_POST["multipleChoiceChoice{$i}_{$j}"];
                    $sqlChoiceUpdate = "UPDATE choices SET choice_text=? WHERE question_id = ? AND idchoice= ?";
                    $stmtChoiceUpdate = $conn->prepare($sqlChoiceUpdate);
                    $stmtChoiceUpdate -> bind_param('sii', $choiceText, $questionId, $choiceId);
                    $stmtChoiceUpdate -> execute();
                }
            }
        } 
        echo "<script>alert('Đề đã được cập nhật thành công!');" ."window.location.href='../public/trangchu.php';"."</script>";
    }
    else echo "<script>alert('Cập nhật đề thất bại, vui lòng kiểm tra lại!');" ."window.location.href='../suade.php';"."</script>";
}
    exit(); // Kết thúc để không chạy tiếp các phần khác của file
?>