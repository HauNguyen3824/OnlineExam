<?php
    include('../conn/conn_database.php');
    include('../template/Tmenubar.php');
    session_start();

    // Fetch data from Exams table
    $query = "SELECT * FROM Exams";
    $result = $conn->query($query);

    if (isset($_SESSION['success_message'])) {
        echo '<script>alert("' . $_SESSION['success_message'] . '");</script>';
        unset($_SESSION['success_message']); // Xóa thông báo khỏi session sau khi hiển thị
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ngân Hàng Đề</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style> 
    tr {
        text-align: center;
    }
</style>
<body>
    <header class="bg-primary text-white text-center py-3">
         <h1>Ngân Hàng Đề</h1>
    </header>

    <div class="container mt-4">
        <h2 class="text-primary">Danh Sách Đề Thi</h2>

        <div class="text-end mb-3">
            <button class="btn btn-primary" onclick="toggleForm()">Thêm Đề Thi Mới</button>
        </div>

        <div id="test-form" class="card p-3 mb-4" style="display: none;">
            <h3 class="text-primary">Thêm/Sửa Thông Tin Đề Thi</h3>
            <form id="form" action="../conn/save_exam.php" method="POST">
            <input type="hidden" id="exam-id" name="examId">
                <div class="mb-3">
                    <label for="test-name" class="form-label">Tên đề thi:</label>
                    <input type="text" id="test-name" name="test-name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Môn học:</label>
                    <select id="subject" name="subject" class="form-select" required>
                        <option value="Toán">Toán</option>
                        <option value="Lý">Lý</option>
                        <option value="Hóa">Hóa</option>
                        <option value="Sinh">Sinh</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="difficulty" class="form-label">Độ khó:</label>
                    <select id="difficulty" name="difficulty" class="form-select" required>
                        <option value="Dễ">Dễ</option>
                        <option value="Trung Bình">Trung Bình</option>
                        <option value="Khó">Khó</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="num-questions" class="form-label">Số lượng câu hỏi:</label>
                    <input type="number" id="num-questions" name="num-questions" class="form-control" min="0" required>
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">Thời lượng (hh:mm:ss):</label>
                    <input type="text" id="duration" name="duration" class="form-control" placeholder="00:00:00" required>
                </div>

                <button type="submit" class="btn btn-success" onclick="saveTest()">Lưu</button>
            </form>
        </div>

        <table class="table table-bordered" id="test-table">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>Tên Đề Thi</th>
                    <th>Môn Học</th>
                    <th>Độ Khó</th>
                    <th>Số Lượng Câu Hỏi</th>
                    <th>Thời Lượng</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['ExamId']) ?></td>
                    <td><?= htmlspecialchars($row['ExamTitle']) ?></td>
                    <td><?= htmlspecialchars($row['Subject']) ?></td>
                    <td><?= htmlspecialchars($row['Difficult']) ?></td>
                    <td><?= htmlspecialchars($row['NumOfQues']) ?></td>
                    <td><?= htmlspecialchars($row['Duration']) ?></td>
                    <td>
                        <button class="btn btn-success btn-sm" onclick="editTest(this)">Sửa</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteTest(this)">Xóa</button>
                        <button class="btn btn-warning btn-sm" onclick="addQuestion(this)">Thêm Câu Hỏi</button>
                        <button class='btn btn-sm btn-primary' onclick='addUser(this)'>Thêm Người Dùng</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>

        </table>
    </div>

    <footer class="text-center py-4">
        <p>© Nhóm 6 - Website Tạo Đề Thi Trắc Nghiệm</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        function toggleForm() {
            const form = document.getElementById('test-form');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        function saveTest() {
            const testName = document.getElementById('test-name').value;
            const subject = document.getElementById('subject').value;
            const difficulty = document.getElementById('difficulty').value;
            const numQuestions = document.getElementById('num-questions').value;
            const duration = document.getElementById('duration').value;

            if (!testName || !subject || !difficulty || !numQuestions || !duration) {
                return alert('Vui lòng điền đầy đủ thông tin!');
            }

            $.ajax({
                url: '../conn/save_exam.php',
                type: 'POST',
                data: {
                    'test-name': testName,
                    'subject': subject,
                    'difficulty': difficulty,
                    'num-questions': numQuestions,
                    'duration': duration
                },
                success: function(response) {
                    if (response.startsWith("success:")) { // Kiểm tra response bắt đầu bằng "success:"
                        const examId = response.substring(8); // Lấy ExamId từ response
                        // Thêm hàng mới vào bảng
                        const table = document.getElementById('test-table').getElementsByTagName('tbody')[0];
                        const newRow = table.insertRow();

                        newRow.innerHTML = `
                            <td>${examId}</td>
                            <td>${testName}</td>
                            <td>${subject}</td>
                            <td>${difficulty}</td>
                            <td>${numQuestions}</td>
                            <td>${duration}</td>
                            <td>
                                <button class="btn btn-success btn-sm" onclick="editTest(this)">Sửa</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteTest(this)">Xóa</button>
                                <button class="btn btn-warning btn-sm" onclick="addQuestion(this)">Thêm Câu Hỏi</button>
                                <button class="btn btn-warning btn-sm" onclick="addUser(this)">Thêm Người Dùng</button>
                            </td>`;

                        toggleForm();
                        document.getElementById('form').reset();
                        alert('Đề thi đã được lưu thành công!');
                        window.location.href = "../PageAdmin/PExamManagement.php"; // Reload trang
                    } else {
                        alert('Lỗi khi lưu đề thi: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Lỗi kết nối đến máy chủ: ' + error);
                }
            });
        }

        function editTest(button) {
            const row = button.parentElement.parentElement;
            document.getElementById('test-name').value = row.cells[1].innerText;
            document.getElementById('subject').value = row.cells[2].innerText;
            document.getElementById('difficulty').value = row.cells[3].innerText;
            document.getElementById('num-questions').value = row.cells[4].innerText;
            document.getElementById('duration').value = row.cells[5].innerText;

            toggleForm();
            row.remove();
        }

        function deleteTest(button) {
            if (confirm('Bạn có chắc chắn muốn xóa đề thi này?')) {
                const row = button.closest('tr'); // Không cần jQuery
                const examId = row.cells[0].textContent.trim(); // Dùng cells[0] thay vì find('td:eq(0)')

                fetch('../conn/delete_exam.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `delete_exam=true&ExamIdToDelete=${encodeURIComponent(examId)}` // encodeURIComponent quan trọng
                })
                .then(response => response.text())
                .then(data => {
                    console.log("Server response:", data);

                    if (data.trim() === 'success') {
                        row.remove();
                        alert('Đề thi đã được xóa thành công!');
                    } else if (data.startsWith("error:")) { // Xử lý lỗi từ server
                        alert(data.substring(6)); // Hiển thị thông báo lỗi bỏ "error:"
                    } else {
                        alert('Lỗi khi xóa đề thi: ' + data); //  cho các lỗi khác
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    alert('Lỗi kết nối đến máy chủ.');
                });
            }
        }
        
        function addQuestion(button) {
            const row = button.closest('tr');
            const examId = row.cells[0].textContent.trim();
            window.location.href = `PAddQToE.php?examid=${encodeURIComponent(examId)}`;
        }
        function addUser(button) {
            const row = button.closest('tr');
            const examId = row.cells[0].textContent.trim();
            window.location.href = `PAddUToAE.php?examid=${encodeURIComponent(examId)}`;
        }
    </script>
</body>
</html>

<?php
include('../template/Tfooter.php');
$conn->close();
?>
