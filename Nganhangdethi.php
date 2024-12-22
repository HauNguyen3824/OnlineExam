<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngân Hàng Đề</title>
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

        .test-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .test-table th, .test-table td {
            border: 1px solid #ddd;
            padding: 0.75rem;
            text-align: left;
        }

        .test-table th {
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

        .add-test {
            text-align: right;
            margin: 1rem 0;
        }

        .add-test button {
            background-color: #355e3b;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #test-form {
            display: none;
            margin: 2rem 0;
            padding: 1rem;
            background-color: #eaf4ea;
            border-radius: 8px;
        }

        #test-form label {
            display: block;
            margin: 0.5rem 0 0.25rem;
            color: #355e3b;
        }

        #test-form input, #test-form select, #test-form button {
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
        Ngân Hàng Đề
    </header>

    <div class="container">
        <h2>Danh Sách Đề Thi</h2>

        <div class="add-test">
            <button onclick="toggleForm()">Thêm Đề Thi Mới</button>
        </div>

        <div id="test-form">
            <h3>Thêm/Sửa Đề Thi</h3>
            <form id="form">
                <label for="test-name">Tên đề thi:</label>
                <input type="text" id="test-name" name="test-name" required>

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

                <label for="num-questions">Số lượng câu hỏi:</label>
                <input type="number" id="num-questions" name="num-questions" required>

                <button type="button" onclick="saveTest()">Lưu</button>
            </form>
        </div>

        <table class="test-table" id="test-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Đề Thi</th>
                    <th>Môn Học</th>
                    <th>Độ Khó</th>
                    <th>Số Lượng Câu Hỏi</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Kiểm tra học kỳ 1</td>
                    <td>Toán</td>
                    <td>Trung Bình</td>
                    <td>20</td>
                    <td>
                        <div class="action-buttons">
                            <button class="edit-btn" onclick="editTest(this)">Sửa</button>
                            <button class="delete-btn" onclick="deleteTest(this)">Xóa</button>
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
            const form = document.getElementById('test-form');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        function saveTest() {
            const table = document.getElementById('test-table').getElementsByTagName('tbody')[0];
            const testName = document.getElementById('test-name').value;
            const subject = document.getElementById('subject').value;
            const difficulty = document.getElementById('difficulty').value;
            const numQuestions = document.getElementById('num-questions').value;

            if (!testName || !subject || !difficulty || !numQuestions) {
                return alert('Vui lòng điền đầy đủ thông tin!');
            }

            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td>${table.rows.length}</td>
                <td>${testName}</td>
                <td>${subject}</td>
                <td>${difficulty}</td>
                <td>${numQuestions}</td>
                <td>
                    <div class="action-buttons">
                        <button class="edit-btn" onclick="editTest(this)">Sửa</button>
                        <button class="delete-btn" onclick="deleteTest(this)">Xóa</button>
                    </div>
                </td>`;

            toggleForm();
            document.getElementById('form').reset();
        }

        function editTest(button) {
            const row = button.parentElement.parentElement.parentElement;
            document.getElementById('test-name').value = row.cells[1].innerText;
            document.getElementById('subject').value = row.cells[2].innerText;
            document.getElementById('difficulty').value = row.cells[3].innerText;
            document.getElementById('num-questions').value = row.cells[4].innerText;

            toggleForm();
            row.remove();
        }

        function deleteTest(button) {
            if (confirm('Bạn có chắc chắn muốn xóa đề thi này?')) {
                const row = button.parentElement.parentElement.parentElement;
                row.remove();
            }
        }
    </script>
</body>
</html>
