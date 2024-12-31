<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PDoingExam.php
session_start();
include('../conn/conn_database.php');

// Lấy examId và userId từ URL
$examId = $_GET['examId'] ?? null;
$userId = $_GET['userId'] ?? null;

if (!$examId || !$userId) {
    die("Không có đề thi nào được chọn hoặc không có người dùng nào được xác định.");
}

// Truy vấn để lấy AUId từ bảng AddUsers
$sql_auid = "SELECT AUId FROM AddUser WHERE ExamId = ? AND UserId = ?";
$stmt_auid = $conn->prepare($sql_auid);
if ($stmt_auid) {
    $stmt_auid->bind_param("ss", $examId, $userId);
    $stmt_auid->execute();
    $result_auid = $stmt_auid->get_result();
    $auid_row = $result_auid->fetch_assoc();
    $stmt_auid->close();

    if (!$auid_row) {
        die("Không tìm thấy AUId cho người dùng và đề thi này.");
    }

    $auid = $auid_row['AUId'];
} else {
    die("Lỗi truy vấn: " . $conn->error);
}

// Lưu AUId vào session
$_SESSION['auid'] = $auid;

// Lấy thời gian hiện tại và lưu vào session
$timeStart = date('Y-m-d H:i:s');
$_SESSION['timeStart'] = $timeStart;

// Truy vấn thông tin đề thi
$sql_exam = "SELECT * FROM Exams WHERE ExamId = ?";
$stmt_exam = $conn->prepare($sql_exam);
$stmt_exam->bind_param("s", $examId);
$stmt_exam->execute();
$result_exam = $stmt_exam->get_result();
$exam = $result_exam->fetch_assoc();

if (!$exam) {
    die("Không tìm thấy đề thi.");
}

// Truy vấn danh sách các quesId của đề thi
$sql_ques_ids = "SELECT QuesId FROM AddQues WHERE ExamId = ?";
$stmt_ques_ids = $conn->prepare($sql_ques_ids);
$stmt_ques_ids->bind_param("s", $examId);
$stmt_ques_ids->execute();
$result_ques_ids = $stmt_ques_ids->get_result();

$quesIds = [];
while ($row = $result_ques_ids->fetch_assoc()) {
    $quesIds[] = $row['QuesId'];
}

// $stmt_exam->close();
// $stmt_ques_ids->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Làm bài thi</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 1200px;
        }
        .exam-container {
            display: flex;
            flex-wrap: wrap;
        }
        .exam-info {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
            flex: 1 1 300px;
            max-width: 300px;
            margin-right: 20px;
            height: fit-content;
            position: sticky;
            top: 20px; /* Khoảng cách từ đỉnh trang */
        }
        .questions-container {
            flex: 3 1 600px;
        }
        .question {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .question-title {
            font-weight: bold;
            font-size: 1.2em;
            color: #333;
        }
        .question-number {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .navigation {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
            flex: 1 1 200px;
            max-width: 200px;
            margin-left: 20px;
            height: fit-content;
            display: flex;
            flex-wrap: wrap;
            position: sticky;
            top: 20px; /* Khoảng cách từ đỉnh trang */
        }
        .nav-button {
            flex: 1 1 calc(20% - 10px); /* Adjust the percentage to fit 4-5 buttons per row */
            margin: 5px;
            text-align: center;
        }
        .completed {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }
        #time-remaining {
            color: red;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Làm bài thi</h1>
    </header>
    <div class="container mt-4">
        <div class="exam-container">
            <div class="exam-info">
                <div class="form-group">
                    <label for="examId">Exam ID</label>
                    <input type="text" class="form-control" id="examId" name="examId" value="<?= htmlspecialchars($examId) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="examTitle">Tên đề thi</label>
                    <input type="text" class="form-control" id="examTitle" name="examTitle" value="<?= htmlspecialchars($exam['ExamTitle']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="duration">Thời gian</label>
                    <input type="text" class="form-control" id="duration" name="duration" value="<?= htmlspecialchars($exam['Duration']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="numOfQues">Số lượng câu hỏi</label>
                    <input type="text" class="form-control" id="numOfQues" name="numOfQues" value="<?= htmlspecialchars($exam['NumOfQues']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="subject">Môn</label>
                    <input type="text" class="form-control" id="subject" name="subject" value="<?= htmlspecialchars($exam['Subject']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="difficult">Độ khó</label>
                    <input type="text" class="form-control" id="difficult" name="difficult" value="<?= htmlspecialchars($exam['Difficult']) ?>" readonly>
                </div>
            </div>
            <div class="questions-container">
            <form id="examForm" method="post" action="../conn/submit_exam.php?examId=<?= htmlspecialchars($examId) ?>&userId=<?= htmlspecialchars($userId) ?>&auid=<?= htmlspecialchars($auid) ?>">
                    <h3>Các câu hỏi</h3>
                    <?php $questionNumber = 1; ?>
                    <?php foreach ($quesIds as $quesId): ?>
                        <?php
                        // Truy vấn thông tin câu hỏi và đáp án
                        $sql_question = "SELECT * FROM Questions WHERE QuesId = ?";
                        $stmt_question = $conn->prepare($sql_question);
                        $stmt_question->bind_param("s", $quesId);
                        $stmt_question->execute();
                        $result_question = $stmt_question->get_result();
                        $question = $result_question->fetch_assoc();
                        $stmt_question->close();
                        ?>
                        <div class="question" id="question-<?= $questionNumber ?>">
                            <div class="form-group">
                                <div class="question-number">Câu <?= $questionNumber ?>:</div>
                                <label class="question-title"><?= htmlspecialchars($question['QuesTitle']) ?></label>
                                <div class="ml-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?= $question['QuesId'] ?>]" id="answer1_<?= $question['QuesId'] ?>" value="1">
                                        <label class="form-check-label" for="answer1_<?= $question['QuesId'] ?>">
                                            <?= htmlspecialchars($question['Answer1']) ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answers[<?= $question['QuesId'] ?>]" id="answer2_<?= $question['QuesId'] ?>" value="2">
                                        <label class="form-check-label" for="answer2_<?= $question['QuesId'] ?>">
                                            <?= htmlspecialchars($question['Answer2']) ?>
                                        </label>
                                    </div>
                                    <?php if (!empty($question['Answer3'])): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answers[<?= $question['QuesId'] ?>]" id="answer3_<?= $question['QuesId'] ?>" value="3">
                                            <label class="form-check-label" for="answer3_<?= $question['QuesId'] ?>">
                                                <?= htmlspecialchars($question['Answer3']) ?>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($question['Answer4'])): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answers[<?= $question['QuesId'] ?>]" id="answer4_<?= $question['QuesId'] ?>" value="4">
                                            <label class="form-check-label" for="answer4_<?= $question['QuesId'] ?>">
                                                <?= htmlspecialchars($question['Answer4']) ?>
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php $questionNumber++; ?>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary" onclick="return validateForm()">Nộp bài</button>
                </form>
            </div>
            <div class="navigation">
                <h4>Thời gian còn lại: <span id="time-remaining"></span></h4>
                <h4>Điều hướng câu hỏi</h4>
                <?php foreach ($quesIds as $index => $quesId): ?>
                    <button type="button" class="btn btn-secondary nav-button" id="nav-btn-<?= $quesId ?>" onclick="scrollToQuestion(<?= $index + 1 ?>)">
                        Câu <?= $index + 1 ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function scrollToQuestion(questionNumber) {
            const questionElement = document.getElementById('question-' + questionNumber);
            if (questionElement) {
                questionElement.scrollIntoView({ behavior: 'smooth' });
            }
        }

        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const questionId = this.name.split('[')[1].split(']')[0];
                const navButton = document.getElementById('nav-btn-' + questionId);
                if (navButton) {
                    navButton.classList.add('completed');
                }
            });
        });

        // Chuyển đổi Duration từ định dạng hh:mm:ss sang giây
        function parseDuration(duration) {
            const parts = duration.split(':');
            const hours = parseInt(parts[0], 10);
            const minutes = parseInt(parts[1], 10);
            const seconds = parseInt(parts[2], 10);
            return (hours * 3600) + (minutes * 60) + seconds;
        }

        // JavaScript để đếm ngược thời gian
        const duration = parseDuration(<?= json_encode($exam['Duration']) ?>);
        let timeRemaining = duration;

        function updateTime() {
            const hours = Math.floor(timeRemaining / 3600);
            const minutes = Math.floor((timeRemaining % 3600) / 60);
            const seconds = timeRemaining % 60;
            document.getElementById('time-remaining').textContent = `${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            if (timeRemaining > 0) {
                timeRemaining--;
            } else {
                clearInterval(timerInterval);
                alert('Hết thời gian làm bài! Bài làm đã được tự động nộp.');
                autoSubmitExam();
            }
        }

        const timerInterval = setInterval(updateTime, 1000);
        updateTime(); // Gọi ngay lập tức để hiển thị thời gian ban đầu

        function autoSubmitExam() {
            const questions = document.querySelectorAll('.question');
            questions.forEach(question => {
                const inputs = question.querySelectorAll('input[type="radio"]');
                let answered = false;
                inputs.forEach(input => {
                    if (input.checked) {
                        answered = true;
                    }
                });
                if (!answered) {
                    // Nếu câu hỏi chưa được trả lời, đặt giá trị là 0
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `answers[${question.dataset.quesId}]`;
                    hiddenInput.value = 0;
                    document.getElementById('examForm').appendChild(hiddenInput);
                }
            });
            const form = document.getElementById('examForm');
            form.action = `../conn/submit_exam.php?examId=${encodeURIComponent('<?= $examId ?>')}&userId=${encodeURIComponent('<?= $userId ?>')}&auid=${encodeURIComponent('<?= $auid ?>')}`;
            form.submit();
        }

        function validateForm() {
            const questions = document.querySelectorAll('.question');
            for (let i = 0; i < questions.length; i++) {
                const question = questions[i];
                const inputs = question.querySelectorAll('input[type="radio"]');
                let answered = false;
                for (let j = 0; j < inputs.length; j++) {
                    if (inputs[j].checked) {
                        answered = true;
                        break;
                    }
                }
                if (!answered) {
                    alert('Bạn phải trả lời tất cả các câu hỏi trước khi nộp bài.');
                    scrollToQuestion(i + 1);
                    return false;
                }
            }
            const form = document.getElementById('examForm');
            form.action = `../conn/submit_exam.php?examId=${encodeURIComponent('<?= $examId ?>')}&userId=${encodeURIComponent('<?= $userId ?>')}&auid=${encodeURIComponent('<?= $auid ?>')}`;
            return true;
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>