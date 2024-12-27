<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngân Hàng Câu Hỏi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #355e3b; /* Màu xanh rêu */
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #355e3b;
        }

        .question-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .question-table th, .question-table td {
            border: 1px solid #ddd;
            padding: 0.75rem;
            text-align: left;
        }

        .question-table th {
            background-color: #355e3b;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-buttons button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #4caf50;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        .add-question {
            text-align: right;
            margin: 1rem 0;
        }

        .add-question button {
            background-color: #355e3b;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #question-form {
            display: none;
            margin: 2rem 0;
            padding: 1rem;
            background-color: #eaf4ea;
            border-radius: 8px;
        }

        #question-form label {
            display: block;
            margin: 0.5rem 0 0.25rem;
            color: #355e3b;
        }

        #question-form input, #question-form select, #question-form button {
            width: 100%;
            padding: 0.5rem;
            margin: 0.25rem 0 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        footer {
            margin-top: 2rem;
            text-align: center;
            padding: 1rem;
            background-color: #355e3b;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        Ngân Hàng Câu Hỏi
    </header>

    <div class="container">
        <h2>Danh Sách Câu Hỏi</h2>

        <div class="add-question">
            <button onclick="toggleForm()">Thêm Câu Hỏi Mới</button>
        </div>

        <div id="question-form">
            <h3>Thêm/Sửa Câu Hỏi</h3>
            <form id="form">
                <label for="question">Nội dung câu hỏi:</label>
                <input type="text" id="question" name="question" required>

                <label for="answer1">Đáp án 1:</label>
                <input type="text" id="answer1" name="answer1" required>

                <label for="answer2">Đáp án 2:</label>
                <input type="text" id="answer2" name="answer2" required>

                <label for="answer3">Đáp án 3:</label>
                <input type="text" id="answer3" name="answer3" required>

                <label for="answer4">Đáp án 4:</label>
                <input type="text" id="answer4" name="answer4" required>

                <label for="correct-answer">Đáp án đúng:</label>
                <select id="correct-answer" name="correct-answer" required>
                    <option value="1">Đáp án 1</option>
                    <option value="2">Đáp án 2</option>
                    <option value="3">Đáp án 3</option>
                    <option value="4">Đáp án 4</option>
                </select>

                <label for="subject">Môn học:</label>
                <select id="subject" name="subject" required>
                    <option value="Toán">Toán</option>
                    <option value="Lý">Lý</option>
                    <option value="Hóa">Hóa</option>
                    <option value="Sinh">Sinh</option>
                </select>

                <label for="difficulty">Độ khó:</label>
                <select id="difficulty" name="difficulty" required>
                    <option value="Dễ">Dễ</option>
                    <option value="Trung Bình">Trung Bình</option>
                    <option value="Khó">Khó</option>
                </select>

                <button type="button" onclick="saveQuestion()">Lưu</button>
            </form>
        </div>

        <table class="question-table" id="question-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nội Dung</th>
                    <th>Đáp Án</th>
                    <th>Đáp Án Đúng</th>
                    <th>Môn Học</th>
                    <th>Độ Khó</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Định lý Pythagoras áp dụng trong tam giác vuông?</td>
                    <td>A: a<sup>2</sup> + b<sup>2</sup> = c<sup>2</sup>, B: a + b = c, C: a<sup>2</sup> - b<sup>2</sup> = c<sup>2</sup>, D: a/b = c</td>
                    <td>A</td>
                    <td>Toán</td>
                    <td>Dễ</td>
                    <td>
                        <div class="action-buttons">
                            <button class="edit-btn" onclick="editQuestion(this)">Sửa</button>
                            <button class="delete-btn" onclick="deleteQuestion(this)">Xóa</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <footer>
        &copy; Nhóm 6 OnlineExam
    </footer>

    <script>
        function toggleForm() {
            const form = document.getElementById('question-form');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        function saveQuestion() {
            const table = document.getElementById('question-table').getElementsByTagName('tbody')[0];
            const question = document.getElementById('question').value;
            const answer1 = document.getElementById('answer1').value;
            const answer2 = document.getElementById('answer2').value;
            const answer3 = document.getElementById('answer3').value;
            const answer4 = document.getElementById('answer4').value;
            const correctAnswer = document.getElementById('correct-answer').value;
            const subject = document.getElementById('subject').value;
            const difficulty = document.getElementById('difficulty').value;

            if (!question || !answer1 || !answer2 || !answer3 || !answer4 || !correctAnswer || !subject || !difficulty) {
                return alert('Vui lòng điền đầy đủ thông tin!');
            }

            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td>${table.rows.length}</td>
                <td>${question}</td>
                <td>A: ${answer1}, B: ${answer2}, C: ${answer3}, D: ${answer4}</td>
                <td>${['A', 'B', 'C', 'D'][correctAnswer - 1]}</td>
                <td>${subject}</td>
                <td>${difficulty}</td>
                <td>
                    <div class="action-buttons">
                        <button class="edit-btn" onclick="editQuestion(this)">Sửa</button>
                        <button class="delete-btn" onclick="deleteQuestion(this)">Xóa</button>
                    </div>
                </td>`;

            toggleForm();
            document.getElementById('form').reset();
        }

        function editQuestion(button) {
            const row = button.parentElement.parentElement.parentElement;
            document.getElementById('question').value = row.cells[1].innerText;

            const answers = row.cells[2].innerText.split(', ').map(a => a.split(': ')[1]);
            document.getElementById('answer1').value = answers[0];
            document.getElementById('answer2').value = answers[1];
            document.getElementById('answer3').value = answers[2];
            document.getElementById('answer4').value = answers[3];

            const correctAnswer = ['A', 'B', 'C', 'D'].indexOf(row.cells[3].innerText) + 1;
            document.getElementById('correct-answer').value = correctAnswer;

            document.getElementById('subject').value = row.cells[4].innerText;
            document.getElementById('difficulty').value = row.cells[5].innerText;

            toggleForm();
            row.remove();
        }

        function deleteQuestion(button) {
            if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
                const row = button.parentElement.parentElement.parentElement;
                row.remove();
            }
        }
    </script>
</body>
</html>
