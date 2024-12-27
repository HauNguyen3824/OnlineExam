<?php
// Kết nối cơ sở dữ liệu
include('../conn/conn_database.php');
session_start();

// Kiểm tra nếu có ExamId được truyền qua GET hoặc từ Session
if (isset($_GET['exam_id'])) {
    // Nếu dữ liệu được truyền qua URL (GET), vệ sinh nó
    $examId = filter_var($_GET['exam_id'], FILTER_SANITIZE_STRING);
    $_SESSION['exam_id'] = $examId; // Lưu vào Session
} elseif (isset($_SESSION['exam_id'])) {
    // Nếu dữ liệu đã có trong Session, lấy nó từ Session
    $examId = filter_var($_SESSION['exam_id'], FILTER_SANITIZE_STRING);
} else {
    // Nếu không tìm thấy ExamId, dừng chương trình và thông báo lỗi
    die("ExamId không tồn tại! Vui lòng quay lại bước khởi tạo.");
}

// Truy vấn thông tin đề thi từ cơ sở dữ liệu
$query = "SELECT ExamTitle, ExamCode FROM Exams WHERE ExamId = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    // Kiểm tra lỗi khi chuẩn bị câu truy vấn
    die("Lỗi khi chuẩn bị câu truy vấn: " . $conn->error);
}

// Liên kết tham số vào câu truy vấn
$stmt->bind_param('s', $examId);

// Thực thi câu truy vấn
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu có kết quả trả về
$examData = $result->fetch_assoc();
if (!$examData) {
    // Nếu không tìm thấy đề thi với ExamId này, ghi log lỗi và thông báo
    error_log("Không tìm thấy đề thi với ExamId: " . htmlspecialchars($examId));
    die("Không tìm thấy thông tin đề thi! Vui lòng kiểm tra lại.");
}

// Lưu dữ liệu vào Session
$_SESSION['exam_title'] = htmlspecialchars($examData['ExamTitle']);
$_SESSION['exam_code'] = htmlspecialchars($examData['ExamCode']);

// Lấy dữ liệu từ Session để sử dụng trong form
$examTitle = $_SESSION['exam_title'];
$examCode = $_SESSION['exam_code'];

// Kiểm tra xem dữ liệu đã được lưu vào Session chưa
if (empty($examTitle) || empty($examCode)) {
    die("Không tìm thấy thông tin đề thi trong Session.");
}

// Xử lý khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $duration = isset($_POST['duration']) ? trim($_POST['duration']) : '';
    $mixQuestions = isset($_POST['mixQuestions']) ? trim($_POST['mixQuestions']) : '';
    $numberOfQuestion = isset($_POST['numberOfQuestion']) ? trim($_POST['numberOfQuestion']) : '';
    $examPassword = isset($_POST['examPassword']) ? trim($_POST['examPassword']) : '';

    // Kiểm tra dữ liệu đầu vào
    if (!empty($duration) && !empty($mixQuestions) && !empty($numberOfQuestion) && !empty($examPassword)) {
        // Cập nhật dữ liệu vào cơ sở dữ liệu
        $updateQuery = "UPDATE Exams SET Duration = ?, MixQuestions = ?, NumberOfQuestion = ?, ExamPassword = ? WHERE ExamId = ?";
        $stmtUpdate = $conn->prepare($updateQuery);

        if (!$stmtUpdate) {
            die("Lỗi chuẩn bị câu truy vấn cập nhật: " . $conn->error);
        }

        // Liên kết tham số vào câu truy vấn
        $stmtUpdate->bind_param('sssss', $duration, $mixQuestions, $numberOfQuestion, $examPassword, $examId);

        // Thực thi câu truy vấn
        if ($stmtUpdate->execute()) {
            echo "Cập nhật thông tin bài thi thành công!";
        } else {
            echo "Lỗi khi cập nhật thông tin bài thi: " . $stmtUpdate->error;
        }

        $stmtUpdate->close();
    } else {
        echo "Vui lòng điền đầy đủ thông tin!";
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo đề</title>
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
            margin-right: 320px; /* Tạo khoảng cách để hiển thị sidebar */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Form chính chứa tất cả các form câu hỏi -->
        <form id="formContainer" class="form-container" action="save_question_content.php" method="POST" onsubmit="submitForm(event)">
            <!-- Các form câu hỏi sẽ được tạo ra ở đây -->
        </form>

        <!-- Nút Submit để gửi dữ liệu -->
        <div class="text-center mt-4">
            <button type="submit" form="formContainer" class="btn btn-success">Lưu câu hỏi</button>
        </div>

    </div>

    <!-- Sidebar bên phải -->
    <div class="sidebar">
        <form method="POST" action="../conn/save_exam_content.php">
            <h4>Thông tin tổng quát</h4>
            
            <!-- Mã đề thi (không thể chỉnh sửa) -->
            <div class="form-group">
                <label for="examId">Exam ID:</label>
                <input type="text" class="form-control" id="examId" name="examId" value="<?php echo htmlspecialchars($examId); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="examCode">Mã đề thi:</label>
                <input type="text" class="form-control" id="examCode" name="examCode" value="<?php echo htmlspecialchars($examCode); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="examTitle">Tên đề thi:</label>
                <input type="text" class="form-control" id="examTitle" name="examTitle" value="<?php echo htmlspecialchars($examTitle); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="numQuestions">Số lượng câu hỏi:</label>
                <input type="number" class="form-control" id="numberOfQuestion" name="numberOfQuestion" min="1" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label> <br>
                <input type="password" class="form-control" id="examPassword" name="examPassword">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="ShowPass" name="ShowPass" onclick="togglePasswordVisibility()">
                <label class="form-check-label" for="ShowPass">Hiện mật khẩu</label>
            </div>

            <div class="form-group">
                <label for="examDuration">Thời gian thi:</label>
                <div class="d-flex">
                    <input type="number" class="form-control mr-2" id="hours" name="hours" placeholder="Giờ" min="0" max="23" required>
                    <input type="number" class="form-control mr-2" id="minutes" name="minutes" placeholder="Phút" min="0" max="59" required>
                    <input type="number" class="form-control" id="seconds" name="seconds" placeholder="Giây" min="0" max="59" required>
                </div>
                <input type="text" name="duration" id="duration" class="form-control mt-2" readonly placeholder="hh:mm:ss">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="mixQuestions" name="mixQuestions">
                <label class="form-check-label" for="mixQuestions">Trộn câu hỏi</label>
            </div>

            <div class="text-center mt-3">
                <button type="submit" name="createExam" class="btn btn-primary">Tạo đề</button>
            </div>

            <div class="text-center mt-3">
                <a href="PMenu.php" class="btn btn-danger" onclick="return confirmExit()">Thoát</a> <!-- Nút thoát -->
            </div>
        </form>
    </div>

    <!-- Container để hiển thị các câu hỏi -->
    <div class="container mt-5">
        <div id="formsContainer" class="mt-3"></div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("examPassword");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }

        // Hàm tạo câu hỏi động
        function generateForms() {
            const numForms = document.getElementById('numberOfQuestion').value;  // Lấy số lượng câu hỏi từ input
            const formsContainer = document.getElementById('formsContainer');  // Chọn container chứa các form câu hỏi
            formsContainer.innerHTML = '';  // Xóa các form cũ

            // Lấy dữ liệu đã lưu từ localStorage (nếu có)
            const savedData = JSON.parse(localStorage.getItem('formData')) || {};

            // Tạo các form câu hỏi động dựa trên số lượng câu hỏi
            for (let i = 1; i <= numForms; i++) {
                const form = document.createElement('div');
                form.id = `dynamicForm${i}`;
                form.classList.add('border', 'p-3', 'mt-4', 'bg-light');
                form.innerHTML = `
                        <h4 class="text-center">Câu ${i}</h4>
                        <div class="form-group">
                            <label for="formName${i}">Tên câu hỏi:</label>
                            <input type="text" class="form-control" id="formName${i}" name="formName${i}" 
                                value="${savedData[`formName${i}`] || ''}" required>
                        </div>
                        <div class="form-group">
                            <label for="choicesContainer${i}">Đáp án:</label>
                            <div id="choicesContainer${i}">
                                ${generateStaticChoices(i, savedData)}  <!-- Tạo các đáp án mặc định -->
                            </div>
                            <label for="correctChoice${i}">Đáp án đúng:</label>
                            <input type="text" class="form-control" id="correctChoice${i}" name="correctChoice${i}" 
                                value="${savedData[`correctChoice${i}`] || ''}" required>
                        </div>
                        <!-- Tính điểm -->
                        <div id="scoreInput${i}">
                            <div class="form-group">
                                <label for="score${i}">Số điểm:</label>
                                <input type="number" class="form-control" id="score${i}" name="score${i}" 
                                    value="${savedData[`score${i}`] || ''}" min="0" required>
                            </div>
                        </div>
                        <!-- Thêm checkbox MixAnswer -->
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="mixAnswer${i}" name="mixAnswer${i}" 
                                ${savedData[`mixAnswer${i}`] ? 'checked' : ''}>
                            <label class="form-check-label" for="mixAnswer${i}">Xáo trộn đáp án</label>
                        </div>
                `;
                formsContainer.appendChild(form);  // Thêm form vào container
            }
        }

        // Hàm tạo sẵn 4 trường đáp án và lưu giá trị vào localStorage
        function generateStaticChoices(formIndex, savedData) {
            let html = '';
            for (let i = 1; i <= 4; i++) {
                html += `
                    <input type="text" class="form-control mb-2" id="choice${formIndex}_${i}" 
                        name="choice${formIndex}_${i}" placeholder="Đáp án ${i}" 
                        value="${savedData[`choice${formIndex}_${i}`] || ''}" required>
                `;
            }
            return html;
        }

        // Sự kiện khi số lượng câu hỏi thay đổi
        document.getElementById('numberOfQuestion').addEventListener('change', generateForms);

        // Lưu dữ liệu vào localStorage khi người dùng nhập thông tin
        window.addEventListener('input', () => {
            const formData = {};
            for (let i = 1; i <= document.getElementById('numQuestions').value; i++) {
                formData[`formName${i}`] = document.getElementById(`formName${i}`).value;
                formData[`correctChoice${i}`] = document.getElementById(`correctChoice${i}`).value;
                formData[`score${i}`] = document.getElementById(`score${i}`).value;
                formData[`mixAnswer${i}`] = document.getElementById(`mixAnswer${i}`).checked;

                // Lưu dữ liệu các đáp án
                for (let j = 1; j <= 4; j++) {
                    formData[`choice${i}_${j}`] = document.getElementById(`choice${i}_${j}`).value;
                }
            }
            localStorage.setItem('formData', JSON.stringify(formData));
        });

        // Hàm xác nhận khi thoát
        function confirmExit() {
            return confirm("Bạn có chắc chắn muốn thoát không?");
        }

        // Lấy các trường input
        const hoursInput = document.getElementById('hours');
        const minutesInput = document.getElementById('minutes');
        const secondsInput = document.getElementById('seconds');
        const durationInput = document.getElementById('duration');

        // Hàm cập nhật giá trị duration
        function updateDuration() {
            // Lấy giá trị từ các input, nếu không nhập thì mặc định là "00"
            const hours = hoursInput.value.padStart(2, '0') || '00';
            const minutes = minutesInput.value.padStart(2, '0') || '00';
            const seconds = secondsInput.value.padStart(2, '0') || '00';

            // Hiển thị thời gian theo cú pháp hh:mm:ss
            durationInput.value = `${hours}:${minutes}:${seconds}`;
        }

        // Gắn sự kiện 'input' để tự động cập nhật khi người dùng thay đổi
        hoursInput.addEventListener('input', updateDuration);
        minutesInput.addEventListener('input', updateDuration);
        secondsInput.addEventListener('input', updateDuration);

        function submitFormData() {
            const formsContainer = document.getElementById('formsContainer');
            const formData = [];

            // Lấy dữ liệu từ các form động
            const numForms = formsContainer.children.length;
            for (let i = 0; i < numForms; i++) {
                const questionData = {};
                
                // Lấy tên câu hỏi
                questionData.QuestionTitle = document.getElementById(`formName${i+1}`).value;
                
                // Lấy đáp án
                questionData.Answer1 = document.getElementById(`answer1-${i+1}`).value;
                questionData.Answer2 = document.getElementById(`answer2-${i+1}`).value;
                questionData.Answer3 = document.getElementById(`answer3-${i+1}`).value || ''; // Có thể để trống
                questionData.Answer4 = document.getElementById(`answer4-${i+1}`).value || ''; // Có thể để trống
                
                // Lấy đáp án đúng
                questionData.Correct = document.getElementById(`correctChoice${i+1}`).value;
                
                // Kiểm tra xáo trộn đáp án
                questionData.MixAnswers = document.getElementById(`mixAnswer${i+1}`).checked ? 1 : 0;
                
                // Lấy điểm
                questionData.Score = document.getElementById(`score${i+1}`).value;
                
                // Lấy ExamId từ trường nhập liệu hoặc Session
                questionData.ExamId = document.getElementById('examId').value || sessionStorage.getItem('examId');
                
                // Sinh QuestionId (QUES-AAxxx)
                const subjectCode = 'CS'; // Hoặc ICT tùy vào chủ đề
                const questionNumber = String(i + 1).padStart(3, '0'); // Số thứ tự câu hỏi
                questionData.QuestionId = `QUES-${subjectCode}${questionNumber}`;
                
                // Lưu thông tin câu hỏi vào mảng
                formData.push(questionData);
            }

            // Gửi dữ liệu lên server (gọi đến file PHP)
            fetch('save_question_content.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Dữ liệu đã được lưu:', data);
                alert('Câu hỏi đã được lưu thành công!');
            })
            .catch(error => {
                console.error('Lỗi khi lưu dữ liệu:', error);
                alert('Có lỗi xảy ra trong quá trình lưu dữ liệu!');
            });
        }

        // Hàm xử lý gửi dữ liệu (Submit Form)
        function submitForm(event) {
            event.preventDefault(); // Ngăn chặn gửi form mặc định

            const formData = new FormData(document.getElementById('formContainer')); // Thu thập dữ liệu từ form chính

            // Gửi dữ liệu (chỉ là ví dụ, có thể gửi bằng AJAX hoặc API tùy theo yêu cầu)
            console.log('Dữ liệu đã gửi:', formData);

            // Thông báo khi gửi thành công
            alert("Dữ liệu đã được gửi!");
        }
    </script>
</body>
</html>
