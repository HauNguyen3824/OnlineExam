<?php
session_start();
require 'db_exam_conn.php'; // Kết nối đến cơ sở dữ liệu

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['username'])) {
    header('Location: ../public/dangnhap.html');
    exit();
}

// Hàm tạo mã đề ngẫu nhiên
function generateExamCode($length = 4) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Xử lý form khi người dùng nhấn submit hoặc lưu nháp
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $examName = $_POST['examName'];
    $numQuestions = $_POST['numQuestions'];
    $password = $_POST['password'];
    $startDatetime = $_POST['startDatetime'];
    $endDatetime = $_POST['endDatetime'];
    $mixQuestions = isset($_POST['mixQuestions']) ? 1 : 0;
    $username = $_SESSION['username']; 

    // Tạo mã đề
    if (!isset($_POST['examCode'])) {
        $examCode = generateExamCode();
    } else {
        $examCode = $_POST['examCode'];
    }

    // Kiểm tra xem người dùng nhấn nút "Lưu Nháp" hay "Tạo Đề"
    $status = isset($_POST['saveDraft']) ? 'draft' : 'published';

    // Lưu vào cơ sở dữ liệu
    $sql = "INSERT INTO exams (exam_code, exam_name, num_questions, password, start_datetime, end_datetime, username, mix_questions, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE exam_name=?, num_questions=?, password=?, start_datetime=?, end_datetime=?, mix_questions=?, status=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssissssisssisssi', 
        $examCode, $examName, $numQuestions, $password, $startDatetime, $endDatetime, $username, $mixQuestions, $status,
        $examName, $numQuestions, $password, $startDatetime, $endDatetime, $mixQuestions, $status
    );
    
    if ($stmt->execute()) {
        $examId = $stmt -> insert_id;
        for ($i = 1; $i <= $numQuestions; $i++) {
            $questionText = $_POST["formName$i"];
            $numChoices = $_POST["numChoices$i"];
            $correctChoice = $_POST["correctChoice$i"];
            $score = $_POST["score$i"];
            $mixChoices = isset($_POST["mixChoiceAns$i"]) ? 1 : 0;
            $sqlQuestion = "INSERT INTO questions (exam_id, question_text, correct_answer, score, mix_answers)
                            VALUE (?, ?, ?, ?, ?)";
            $stmtQuestion = $conn->prepare($sqlQuestion);
            $stmtQuestion -> bind_param('issii', $examId, $questionText, $correctChoice, $score, $mixChoices);
            if ($stmtQuestion -> execute()) {
                $questionId = $stmtQuestion -> insert_id;
                for ($j = 1; $j <= $numChoices; $j++) {
                    $choiceText = $_POST["multipleChoiceChoice{$i}_{$j}"];
                    $sqlChoice = "INSERT INTO choices (question_id, choice_text)
                                  VALUES (?, ?)";
                    $stmtChoice = $conn->prepare($sqlChoice);
                    $stmtChoice -> bind_param('is', $questionId, $choiceText);
                    $stmtChoice -> execute();
                }
            }
        }
        if ($status == 'draft') echo "<script>alert('Đề đã được lưu tạm thời! Bạn có thể tiếp tục chỉnh sửa sau.'); window.location.href='../admin/quanlyde.php';</script>";
        else echo "<script>alert('Đề đã được tạo thành công!'); window.location.href='../public/trangchu.php';</script>";
    } 
    else echo "<script>alert('Có lỗi trong quá trình lưu đề. Vui lòng thử lại!');</script>";  
} 
?>
<!DOCTYPE html>
<html lang="en">
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
        p {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Sidebar bên phải -->
    <form method="post">
    <div class="sidebar">
        <h4>Thông tin tổng quát</h4>
        
            <div class="form-group">
                <label for="numQuestions">Số lượng câu hỏi:</label>
                <input type="number" class="form-control" id="numQuestions" name="numQuestions" min="1" required onchange="generateForms()">
            </div>

            <div class="form-group">
                <label for="examName">Tên đề thi:</label>
                <input type="text" class="form-control" id="examName" name="examName" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <div class="input-group">
                    <input type="checkbox" id="showPasswordToggle" onchange="togglePasswordField()">
                    <input type="password" class="form-control ml-2" id="password" name="password">
                </div>
            </div>

            <div class="form-group">
                <label for="startDatetime">Thời gian bắt đầu:</label>
                <input type="datetime-local" class="form-control" id="startDatetime" name="startDatetime" required>
            </div>
            
            <div class="form-group">
                <label for="endDatetime">Thời gian kết thúc:</label>
                <input type="datetime-local" class="form-control" id="endDatetime" name="endDatetime" required>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="mixQuestions" name="mixQuestions">
                <label class="form-check-label" for="mixQuestions">Trộn câu hỏi</label>
            </div>
            <p>Lưu ý: Chúng tôi biết là tạo đề đôi khi có thể mắc sai sót, nhưng mong bạn có thể làm đúng số lượng câu hỏi và số lượng câu trả lời cho mỗi câu hỏi, vì khi sửa đề bạn sẽ KHÔNG THỂ sửa số lượng được nữa.</p>
            <!-- Nút submit -->
            <div class="text-center mt-3">
                <button type="submit" name="saveDraft" class="btn btn-secondary">Lưu Nháp</button>
                <button type="submit" name="createExam" class="btn btn-primary">Tạo đề</button>
            </div>

            <div class="text-center mt-3">
                <a href="../public/trangchu.php" class="btn btn-danger">Thoát</a> <!-- Nút thoát -->
            </div>
    </div>
     <!-- Container để hiển thị các câu hỏi -->
        <div class="container mt-5">
            <div id="formsContainer" class="mt-3"></div>
        </div>
        </form>
   
    <script>
        // Hàm để hiển thị hoặc ẩn ô mật khẩu trong sidebar
        function togglePasswordField() {
            const passwordField = document.getElementById('password');
            if (document.getElementById('showPasswordToggle').checked) {
                passwordField.setAttribute('type', 'text');
            } else {
                passwordField.setAttribute('type', 'password');
            }
        }

        // Tạo câu hỏi động
        function generateForms() {
            const numForms = document.getElementById('numQuestions').value;
            const formsContainer = document.getElementById('formsContainer');
            formsContainer.innerHTML = ''; // Xóa các form cũ

            for (let i = 1; i <= numForms; i++) {
                const form = document.createElement('div');
                form.id = `dynamicForm${i}`;
                form.classList.add('border', 'p-3', 'mt-4', 'bg-light');
                form.innerHTML = `
                    <h4 class="text-center">Câu ${i}</h4>
                    <div class="form-group">
                        <label for="formName${i}">Tên câu hỏi:</label>
                        <input type="text" class="form-control" id="formName${i}" name="formName${i}" required>
                    </div>
                    <div class="form-group">
                        <label for="numChoices${i}">Số lượng đáp án:</label>
                        <input type="number" class="form-control" id="numChoices${i}" name="numChoices${i}" min="1" onchange="generateChoices(${i}, 'multipleChoice')">
                        <div id="choicesContainer${i}"></div>
                        <label for="correctChoice${i}">Đáp án đúng:</label>
                        <input type="text" class="form-control" id="correctChoice${i}" name="correctChoice${i}">
                        <label>Trộn đáp án: <input type="checkbox" id="mixChoiceAns${i}" name="mixChoiceAns${i}"></label><br>
                    </div>
                    <!-- Tính điểm -->
                    <div id="scoreInput${i}">
                        <div class="form-group">
                            <label for="score${i}">Số điểm:</label>
                            <input type="number" class="form-control" id="score${i}" name="score${i}" min="0">
                        </div>
                    </div>
                `;
                formsContainer.appendChild(form);
            }
        }

        // Sinh ra các đáp án (cho câu hỏi chọn đáp án)
        function generateChoices(formIndex, type) {
            const numChoices = document.getElementById(`numChoices${formIndex}`).value;
            const container = document.getElementById(`choicesContainer${formIndex}`);
            container.innerHTML = '';

            for (let i = 1; i <= numChoices; i++) {
                const choice = document.createElement('input');
                choice.type = 'text';
                choice.name = `${type}Choice${formIndex}_${i}`;
                choice.classList.add('form-control', 'mb-2');
                choice.placeholder = `Đáp án ${i}`;
                container.appendChild(choice);
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
