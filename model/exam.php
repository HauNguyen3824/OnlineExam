<?php
session_start();
require 'db_exam_conn.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: dangnhap.html');
    exit();
}

// Lấy ID bài thi từ URL
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

// Truy vấn thông tin bài thi
$exam_query = "SELECT * FROM exams WHERE id = $exam_id";
$exam_result = $conn->query($exam_query);

if ($exam_result->num_rows > 0) {
    $exam = $exam_result->fetch_assoc();
    $current_time = date('Y-m-d H:i:s');

    // Kiểm tra thời gian làm bài
    if ($current_time < $exam['start_time']) {
        echo "<div class='alert alert-warning'>Chưa đến thời gian làm bài!</div>";
        exit; // Dừng nếu chưa đến thời gian
    }

    // Thời gian còn lại
    $end_time = new DateTime($exam['end_time']);
    $start_time = new DateTime($exam['start_time']);
    $duration = $end_time->getTimestamp() - time();

    // Truy vấn câu hỏi
    $questions_query = "SELECT * FROM questions WHERE exam_id = $exam_id";
    $questions_result = $conn->query($questions_query);

    // Lưu câu trả lời vào session nếu POST được gửi
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $answers = $_POST['answers']; // Dạng array ['question_id' => 'answer']
        $_SESSION['exam_answers'][$exam_id] = $answers; // Lưu câu trả lời vào session
        echo "<div class='alert alert-success'>Câu trả lời đã được lưu.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Bài thi không tồn tại.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thi trắc nghiệm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
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
        .question-card {
            border: 1px solid #dee2e6;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
        }
        .question-card h5 {
            margin-bottom: 15px;
        }
        .answer-options {
            list-style-type: none;
            padding-left: 0;
        }
        .answer-options li {
            margin-bottom: 10px;
        }
        .btn-submit {
            margin-top: 20px;
        }
        .fixed-top-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #343a40;
            color: white;
            padding: 10px;
            text-align: center;
            z-index: 1000;
        }
        .timer {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .sidebar-questions {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
            gap: 10px;
        }
        .sidebar-questions button {
            padding: 10px;
            font-size: 1.2rem;
            text-align: center;
        }
        .completed {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="fixed-top-bar">
        <span id="testName">Tên đề thi: <?php echo $exam['name']; ?></span>
        <span class="timer float-right">Thời gian còn lại: <span id="timeRemaining"></span></span>
    </div>

    <div class="sidebar">
        <h4>Danh sách câu hỏi</h4>
        <div class="sidebar-questions">
            <?php for ($i = 1; $i <= $questions_result->num_rows; $i++): ?>
                <button id="questionBtn<?php echo $i; ?>" class="btn btn-outline-secondary" onclick="scrollToQuestion(<?php echo $i; ?>)"><?php echo $i; ?></button>
            <?php endfor; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-submit" onclick="submitAnswers()">Nộp bài</button>
    </div>

    <div class="container mt-5 pt-5">
        <form method="POST" action="">
        <?php while ($question = $questions_result->fetch_assoc()): ?>
            <?php
            // Lấy câu trả lời từ session nếu có
            $saved_answer = isset($_SESSION['exam_answers'][$exam_id][$question['id']]) ? $_SESSION['exam_answers'][$exam_id][$question['id']] : '';
            ?>
            <div class="question-card" id="question<?php echo $question['id']; ?>">
                <h5>Câu <?php echo $question['id']; ?>: <?php echo $question['question_text']; ?></h5>
                <ul class="answer-options">
                    <li>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="A"
                        <?php echo ($saved_answer == 'A') ? 'checked' : ''; ?>> A. <?php echo $question['option_a']; ?>
                    </li>
                    <li>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="B"
                        <?php echo ($saved_answer == 'B') ? 'checked' : ''; ?>> B. <?php echo $question['option_b']; ?>
                    </li>
                    <li>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="C"
                        <?php echo ($saved_answer == 'C') ? 'checked' : ''; ?>> C. <?php echo $question['option_c']; ?>
                    </li>
                    <li>
                        <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="D"
                        <?php echo ($saved_answer == 'D') ? 'checked' : ''; ?>> D. <?php echo $question['option_d']; ?>
                    </li>
                </ul>
            </div>
        <?php endwhile; ?>
        <button type="submit" class="btn btn-success">Lưu câu trả lời</button>
        </form>
    </div>

    <script>
        let completedQuestions = new Set();

        // Hàm đánh dấu câu hỏi đã hoàn thành và thay đổi màu ô tương ứng trong sidebar
        function markQuestionCompleted(questionNumber) {
            document.getElementById(`questionBtn${questionNumber}`).classList.add('completed');
            completedQuestions.add(questionNumber);
        }

        // Hàm cuộn đến câu hỏi tương ứng
        function scrollToQuestion(questionNumber) {
            const question = document.getElementById(`question${questionNumber}`);
            question.scrollIntoView({ behavior: 'smooth' });
        }

        // Hàm kiểm tra đã hoàn thành hết các câu hỏi chưa
        function isTestCompleted() {
            return completedQuestions.size === <?php echo $questions_result->num_rows; ?>;
        }

        // Hàm nộp bài
        function submitAnswers() {
            if (!isTestCompleted()) {
                alert('Bạn chưa hoàn thành bài thi, không thể nộp.');
            } else {
                const confirmSubmit = confirm('Bạn có chắc là muốn nộp bài không?');
                if (confirmSubmit) {
                    alert('Bài thi đã được nộp!');
                    window.location.href = 'trangchu.html';
                }
            }
        }

        // Thiết lập thời gian còn lại
        let timeLeft = <?php echo $duration; ?>; // Thời gian còn lại
        const timerElement = document.getElementById('timeRemaining');

        const countdown = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

            if (timeLeft <= 0) {
                clearInterval(countdown);
                alert('Đã hết thời gian, bài làm tự động lưu và nộp.');
                // Xử lý tự động nộp bài khi hết thời gian
                submitAnswers();
            }

            timeLeft--;
        }, 1000);
    </script>
</body>
</html>
