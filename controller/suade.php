<?php
session_start();
require 'db_exam_conn.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: dangnhap.html');
    exit();
}

// Xử lý yêu cầu POST khi có exam_code được gửi lên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['exam_code'])) {
    $examCode = $_POST['exam_code'];
    $sql = "SELECT * FROM exams WHERE exam_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $examCode);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra xem mã đề có tồn tại không
    if ($result->num_rows > 0) {
        $exam = $result->fetch_assoc();
        $mixQuestion = ($exam['mix_questions'] ? "yes" : "");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa đề</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .hidden { display: none; }
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background-color: #f8f9fa;
            border-left: 1px solid #dee2e6;
            padding: 20px;
            overflow-y: auto;
        }
        .container {
            margin-right: 320px;
        }
    </style>
</head>
<body>

<form method="post" action = "suade1.php">
    <!-- Sidebar bên phải -->
    <div class="sidebar">
        <h4>Thông tin tổng quát</h4>
        <!-- Render các thông tin chung của đề thi -->
        <input type = "hidden" class = "form-control" value = "<?php echo htmlspecialchars($exam['idexam'])?>" name = "examId">
        <input type = "hidden" class = "form-control" value = "<?php echo htmlspecialchars($exam['exam_code'])?>" name = "examCode"> 
        <div class="form-group">
            <label for="numQuestions">Số lượng câu hỏi:</label>
            <input type="number" class="form-control" value="<?php echo htmlspecialchars($exam['num_questions']) ?>" id="numQuestions" name="numQuestions" min="1" readonly>
        </div>
        <div class="form-group">
            <label for="examName">Tên đề thi:</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($exam['exam_name']) ?>" id="examName" name="examName" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <div class="input-group">
                <input type="checkbox" id="showPasswordToggle" onchange="togglePasswordField()">
                <input type="password" class="form-control ml-2" value="<?php echo htmlspecialchars($exam['password']) ?>" id="password" name="password">
            </div>
        </div>
        <div class="form-group">
            <label for="startDatetime">Thời gian bắt đầu:</label>
            <input type="datetime-local" class="form-control" id="startDatetime" value="<?php echo htmlspecialchars($exam['start_datetime']) ?>" name="startDatetime" required>
        </div>
        <div class="form-group">
            <label for="endDatetime">Thời gian kết thúc:</label>
            <input type="datetime-local" class="form-control" id="endDatetime" value="<?php echo htmlspecialchars($exam['end_datetime']) ?>" name="endDatetime" required>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="mixQuestions" name="mixQuestions" value="<?php echo $mixQuestion ?>">
            <label class="form-check-label" for="mixQuestions">Trộn câu hỏi</label>
        </div>
        <!-- Nút submit -->
        <div class="text-center mt-3">
            <button type="submit" name="saveDraft" class="btn btn-secondary">Lưu Nháp</button>
            <button type="submit" name="createExam" class="btn btn-primary">Tạo đề</button>
        </div>
        <div class="text-center mt-3">
            <a href="../public/trangchu.php" class="btn btn-danger">Thoát</a>
        </div>
    </div>

    <!-- Container để hiển thị các câu hỏi -->
    <div class="container mt-5">
        <div id="formsContainer" class="mt-3">
            <?php
            // Truy vấn các câu hỏi liên quan đến đề thi này và render ra các form động
            $sqlQuestions = "SELECT * FROM questions WHERE exam_id = ? ORDER BY idquestion ASC";
            $stmtQuestions = $conn->prepare($sqlQuestions);
            $stmtQuestions->bind_param('i', $exam['idexam']);
            $stmtQuestions->execute();
            $questionsResult = $stmtQuestions->get_result();
            $i = 1;
            $k = 1;
            while ($question = $questionsResult->fetch_assoc()) {
                $j = 0;
                $choices = [];
                
                // Truy vấn các đáp án của câu hỏi
                $sqlChoices = "SELECT * FROM choices WHERE question_id = ?";
                $stmtChoices = $conn->prepare($sqlChoices);
                $stmtChoices->bind_param('i', $question['idquestion']);
                $stmtChoices->execute();
                $choicesResult = $stmtChoices->get_result();
                
                while ($choice = $choicesResult->fetch_assoc()) {
                    $j++;
                    $choice1 = array(
                        'choice_id' => htmlspecialchars($choice['idchoice']),
                        'choice_text' => htmlspecialchars($choice['choice_text'])
                    );
                    $choices[] = $choice1;
                }

                // Render câu hỏi và đáp án
                $questionText = htmlspecialchars($question['question_text']);
                $answer = htmlspecialchars($question['correct_answer']);
                $score = htmlspecialchars($question['score']);
                $check = ($question['mix_answers'] ? 'checked' : '');

                echo "<div class='border p-3 mt-4 bg-light'>";
                echo "<h4 class='text-center'>Câu {$i}</h4>";
                echo "<div class='form-group'>";
                echo "<label for='formName{$i}'>Tên câu hỏi:</label>";
                echo "<input type = 'hidden' class = 'form-control' name = 'questionId{$i}' value = '{$question['idquestion']}'>";
                echo "<input type='text' class='form-control' id='formName{$i}' name='formName{$i}' value='{$questionText}' required>";
                echo "</div>";
                echo "<div class='form-group'>";
                echo "<label>Số lượng đáp án:</label>";
                echo "<input type='number' class='form-control' name = 'numChoice{$i}' value='{$j}' readonly>"."</br>";
                foreach ($choices as $choice) {
                    echo "<input type ='hidden' class = 'form-control' name = 'choice_id{$i}_{$k}' value = '{$choice['choice_id']}'>";
                    echo "<input type='text' class='form-control' name = 'multipleChoiceChoice{$i}_{$k}' value='{$choice['choice_text']}'>";
                    $k++;
                }
                $k = 1;
                echo "<label>Đáp án đúng:</label>";
                echo "<input type='text' class='form-control' name = 'correctChoice{$i}' value='{$answer}'>";
                echo "</div>";
                echo "<label>Số điểm:</label>";
                echo "<input type='number' class='form-control' name = 'score{$i}' value='{$score}'>";
                echo "<label>Trộn đáp án:</label>";
                echo "<input type='checkbox' {$check} name = ''>";
                echo "</div>";
                $i++;
            }
            ?>
        </div>
    </div>
</form>

<script>
    function togglePasswordField() {
        const passwordField = document.getElementById('password');
        passwordField.type = document.getElementById('showPasswordToggle').checked ? 'text' : 'password';
    }
</script>
</body>
</html>
