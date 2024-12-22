<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Đề Thi</title>
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #355e3b;
            margin-bottom: 1rem;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 0.5rem 0 0.25rem;
            color: #355e3b;
        }

        input, select, button {
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        button {
            background-color: #355e3b;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #2d4f33;
        }

        .test-list {
            margin-top: 2rem;
        }

        .test-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .test-list th, .test-list td {
            border: 1px solid #ddd;
            padding: 0.75rem;
            text-align: left;
        }

        .test-list th {
            background-color: #355e3b;
            color: white;
        }

        .action-buttons button {
            margin-right: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
        }

        .edit-btn {
            background-color: #4caf50;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        Tạo Đề Thi
    </header>

    <div class="container">
        <h2>Thông Tin Đề Thi</h2>
        <form id="create-test-form">
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

            <button type="submit">Tạo Đề Thi</button>
        </form>

        <div class="test-list">
            <h2>Danh Sách Đề Thi</h2>
            <table id="test-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Đề Thi</th>
                        <th>Môn Học</th>
                        <th>Độ Khó</th>
                        <th>Số Câu Hỏi</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Danh sách đề thi sẽ được thêm ở đây -->
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        &copy;  Nhóm 6 OnlineExam
    </footer>

    <script>
        const form = document.getElementById('create-test-form');
        const testTableBody = document.getElementById('test-table').getElementsByTagName('tbody')[0];

        let testId = 1; // ID tự tăng cho mỗi đề thi

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const testName = document.getElementById('test-name').value;
            const subject = document.getElementById('subject').value;
            const difficulty = document.getElementById('difficulty').value;
            const numQuestions = document.getElementById('num-questions').value;

            addTestToTable(testId++, testName, subject, difficulty, numQuestions);
            alert(`Đề thi "${testName}" đã được tạo thành công.`);
            form.reset();
        });

        function addTestToTable(id, name, subject, difficulty, numQuestions) {
            const row = testTableBody.insertRow();
            row.innerHTML = `
                <td>${id}</td>
                <td>${name}</td>
                <td>${subject}</td>
                <td>${difficulty}</td>
                <td>${numQuestions}</td>
                <td>
                    <div class="action-buttons">
                        <button class="edit-btn" onclick="editTest(${id})">Sửa</button>
                        <button class="delete-btn" onclick="deleteTest(this)">Xóa</button>
                    </div>
                </td>`;
        }

        function editTest(id) {
            alert(`Chỉnh sửa đề thi với ID: ${id}`);
            // Logic chỉnh sửa đề thi có thể thêm ở đây
        }

        function deleteTest(button) {
            const row = button.parentElement.parentElement.parentElement;
            if (confirm('Bạn có chắc chắn muốn xóa đề thi này?')) {
                row.remove();
            }
        }
    </script>
</body>
</html>
