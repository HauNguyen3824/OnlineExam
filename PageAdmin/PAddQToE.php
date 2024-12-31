<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PAddQToE.php
include('../conn/conn_database.php');

$examId = $_GET['examid'] ?? '';

// Truy vấn thông tin đề thi
$sql_exam_info = "SELECT * FROM Exams WHERE ExamId = ?";
$stmt_exam_info = $conn->prepare($sql_exam_info);
$stmt_exam_info->bind_param("s", $examId);
$stmt_exam_info->execute();
$result_exam_info = $stmt_exam_info->get_result();
$exam_info = $result_exam_info->fetch_assoc();

$examTitle = $exam_info['ExamTitle'] ?? '';
$duration = $exam_info['Duration'] ?? '';
$numOfQues = $exam_info['NumOfQues'] ?? '';
$subject = $exam_info['Subject'] ?? '';
$difficult = $exam_info['Difficult'] ?? '';

$stmt_exam_info->close();

$limit_search_questions = 10;
$page_search_questions = isset($_GET['page_search_questions']) ? (int)$_GET['page_search_questions'] : 1;
$offset_search_questions = ($page_search_questions - 1) * $limit_search_questions;

// Truy vấn tra cứu câu hỏi (tất cả câu hỏi)
$sql_search_questions = "SELECT * FROM Questions LIMIT ? OFFSET ?";
$stmt_search_questions = $conn->prepare($sql_search_questions);
$stmt_search_questions->bind_param("ii", $limit_search_questions, $offset_search_questions);
$stmt_search_questions->execute();
$result_search_questions = $stmt_search_questions->get_result();

// Tính tổng số trang cho tra cứu câu hỏi (tất cả câu hỏi)
$sql_count_search_questions = "SELECT COUNT(*) FROM Questions";
$result_count_search_questions = $conn->query($sql_count_search_questions);
$total_search_questions = $result_count_search_questions->fetch_row()[0];
$totalPages_search_questions = ceil($total_search_questions / $limit_search_questions);

// Lấy danh sách QuesId từ bảng AddQues
$sql_exam_questions = "SELECT QuesId FROM AddQues WHERE ExamId = ?";
$stmt_exam_questions = $conn->prepare($sql_exam_questions);
$stmt_exam_questions->bind_param("s", $examId);
$stmt_exam_questions->execute();
$result_exam_questions = $stmt_exam_questions->get_result();
$exam_questions = [];
while ($row = $result_exam_questions->fetch_assoc()) {
    $exam_questions[] = $row['QuesId'];
}
$stmt_exam_questions->close();

// Nếu có câu hỏi trong đề thi
$quesIds = [];
if (!empty($exam_questions)) {
    $quesId_string = "'" . implode("','", $exam_questions) . "'";

    // Truy vấn thông tin câu hỏi từ bảng Questions
    $sql_question_details = "SELECT * FROM Questions WHERE QuesId IN ($quesId_string)";
    $result_question_details = $conn->query($sql_question_details);

    if (!$result_question_details) { // Bổ sung xử lý lỗi
        die("Lỗi truy vấn chi tiết câu hỏi: " . $conn->error); // returns error for diagnostics
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thêm Câu Hỏi Vào Đề</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     <style>
            .answer-list {
                    white-space: pre-line; /* Enable newlines and extra whitespaces on displayed value*/
                  }
.hidden {
            display: none; /* Use to dynamically control options via Jquery */
            }

        .correct-answers-wrapper { /* correct layout control */
            position: relative;
        }

        .table-container {
            display: flex;
            justify-content: space-between; /* Adjust spacing as needed */
        }

        .table-wrapper {
            width: 48%; /* Adjust width as needed */
            /* Add other styles like borders or margins */
        }
        .nav-item{
            text-align:center;
            padding-left:3%;
        }

     </style>
  </head>
 <body>
    <header class="bg-primary text-white text-center py-3">
         <h1>Thêm Câu Hỏi Vào Đề</h1>
    </header>
        <div class="container mt-4">
            <h2 class="text-primary">Thông tin đề</h2>
            <form id="form">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <label for="examId" class="form-label">Exam ID</label>
                        <input type="text" id="examId" name="examId" class="form-control" value="<?php echo htmlspecialchars($examId); ?>" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="examTitle" class="form-label">Tên đề thi</label>
                        <input type="text" id="examTitle" name="examTitle" class="form-control" value="<?php echo htmlspecialchars($examTitle); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label for="duration" class="form-label">Thời gian</label>
                        <input type="text" id="duration" name="duration" class="form-control" value="<?php echo htmlspecialchars($duration); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label for="numOfQues" class="form-label">Số lượng câu hỏi</label>
                        <input type="text" id="numOfQues" name="numOfQues" class="form-control" value="<?php echo htmlspecialchars($numOfQues); ?>" readonly>
                    </div>
                    <div class="col-md-2">
                        <label for="subject" class="form-label">Môn</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject); ?>" readonly>
                    </div>
                    <div class="col-md-1">
                        <label for="difficult" class="form-label">Độ khó</label>
                        <input type="text" id="difficult" name="difficult" class="form-control" value="<?php echo htmlspecialchars($difficult); ?>" readonly>
                    </div>
                </div>
            </form>
        </div>
      <div class="container mt-4">
        <div class="table-container">  <!-- Container cho cả hai bảng -->
        <div class="table-wrapper"> <!-- Wrapper cho bảng câu hỏi của đề -->
            <h2>Câu hỏi của đề</h2>
            <table class="table table-bordered" id="question-table">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>ID</th>
                        <th>Nội Dung</th>
                        <th>Đáp Án</th>
                        <th>Đáp Án Đúng</th>
                        <th>Môn Học</th> 
                        <th>Lớp</th>
                        <th>Độ Khó</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                
                <?php if (isset($result_question_details) && $result_question_details->num_rows > 0): ?>
                    <?php while ($row = $result_question_details->fetch_assoc()): ?>
                        <tr data-question-id="<?= htmlspecialchars($row['QuesId']) ?>">
                            <td><?= htmlspecialchars($row['QuesId']) ?></td>
                            <td><?= htmlspecialchars($row['QuesTitle']) ?></td>
                            <td class="answer-list">
                                • A: <?= htmlspecialchars($row['Answer1']) ?><br>
                                • B: <?= htmlspecialchars($row['Answer2']) ?><br>
                                <?php if (isset($row['Answer3']) && !empty($row['Answer3'])): ?>
                                    • C: <?= htmlspecialchars($row['Answer3']) ?><br>
                                <?php endif; ?>
                                <?php if (isset($row['Answer4']) && !empty($row['Answer4'])): ?>
                                    • D: <?= htmlspecialchars($row['Answer4']) ?><br>
                                <?php endif; ?>
                            </td>
                            <td><?= ['A', 'B', 'C', 'D'][$row['Correct'] - 1] ?></td>
                            <td><?= htmlspecialchars($row['Subject']) ?></td>
                            <td><?= htmlspecialchars($row['Class']) ?></td>
                            <td><?= htmlspecialchars($row['Difficult']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-danger remove-btn" onclick="removeQuestionFromTable(this, '<?= $row['QuesId'] ?>')">Xóa</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                <?php endif; ?>

                </tbody>
            </table>

            <button id="save-exam-questions" class="btn btn-primary">Lưu câu hỏi của đề</button>
            <button id="exit-add-questions" class="btn btn-secondary">Thoát</button>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center" id="exam_questions_pagination"> </ul>
            </nav>
        </div>


        <div class="table-wrapper">  <!-- Wrapper cho bảng tra cứu -->
            <h2>Tra cứu câu hỏi</h2>
            <form method="get" action="PAddQToE.php">
                <input type="hidden" name="examid" value="<?php echo htmlspecialchars($_GET['examid']); ?>">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="subjectFilter" class="form-label">Môn Học</label>
                        <select id="subjectFilter" name="subjectFilter" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="toán">Toán</option>
                            <option value="lý">Lý</option>
                            <option value="hóa">Hóa</option>
                            <option value="sinh">Sinh</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="classFilter" class="form-label">Lớp</label>
                        <select id="classFilter" name="classFilter" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="difficultyFilter" class="form-label">Độ Khó</label>
                        <select id="difficultyFilter" name="difficultyFilter" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="dễ">Dễ</option>
                            <option value="trung bình">Trung Bình</option>
                            <option value="khó">Khó</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">Tìm kiếm</button>
                    </div>
                </div>
            </form>
            <table class="table table-bordered" id="search-question-table">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>ID</th>
                        <th>Nội Dung</th>
                        <th>Đáp Án</th>
                        <th>Đáp Án Đúng</th>
                        <th>Môn Học</th> 
                        <th>Lớp</th>
                        <th>Độ Khó</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_search_questions && $result_search_questions->num_rows > 0): ?>
                        <?php while ($row = $result_search_questions->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["QuesId"]); ?></td>
                                <td><?php echo htmlspecialchars($row["QuesTitle"]); ?></td>
                                <td class="answer-list">
                                    • A: <?php echo htmlspecialchars($row["Answer1"]); ?><br>
                                    • B: <?php echo htmlspecialchars($row["Answer2"]); ?><br>
                                    <?php if (isset($row['Answer3']) && !empty($row['Answer3'])): ?>
                                        • C: <?php echo htmlspecialchars($row["Answer3"]); ?><br>
                                    <?php endif; ?>
                                    <?php if (isset($row["Answer4"]) && !empty($row["Answer4"])): ?>
                                        • D: <?php echo htmlspecialchars($row["Answer4"]); ?><br>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo ['A', 'B', 'C', 'D'][$row["Correct"] - 1]; ?></td>
                                <td><?php echo htmlspecialchars($row["Subject"]); ?></td>
                                <td><?php echo htmlspecialchars($row["Class"]); ?></td>
                                <td><?php echo htmlspecialchars($row["Difficult"]); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success add-btn" onclick="addQuestionToExam('<?= $row['QuesId'] ?>', '<?= $examId ?>')">Thêm</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align:center">Không có câu hỏi nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center" id="search_questions_pagination"> 
                    <?php for ($i = 1; $i <= $totalPages_search_questions; $i++): ?>
                        <li class="page-item <?php if ($i == $page_search_questions) echo 'active'; ?>">
                            <a class="page-link" href="<?php echo $_SERVER['PHP_SELF'] . '?examid=' . $examId . '&page_search_questions=' . $i ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        </div>
</div>
    <!-- Footer using regular Html properties for simplicity to maintain visual uniformity -->
    <footer class="text-center py-4">
        <p>© Nhóm 6 - Website Tạo Đề Thi Trắc Nghiệm</p>
    </footer>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function addQuestionToExam(quesId, examId) {
        // Lấy giá trị số câu hỏi tối đa từ trường #numOfQues.
        var maxQuestions = parseInt($('#numOfQues').val());

        // Đếm số câu hỏi hiện tại trong bảng question-table.
        var currentCount = $('#question-table tbody tr').length;

        // Kiểm tra nếu đã đạt số lượng tối đa.
        if (currentCount >= maxQuestions) {
            alert('Bạn chỉ được phép thêm tối đa ' + maxQuestions + ' câu hỏi.');
            return;
        }

        // Tìm hàng tương ứng trong bảng search-question-table.
        var row = $('#search-question-table').find('tr:has(button[onclick*="' + quesId + '"])');

        // Kiểm tra nếu hàng đã tồn tại trong bảng question-table.
        if ($('#question-table').find('tr:has(td:contains("' + quesId + '"))').length > 0) {
            alert('Câu hỏi này đã được thêm!');
            return;
        }

        // Clone hàng từ bảng search-question-table.
        var clonedRow = row.clone();

        // Thay đổi nút "Thêm" thành nút "Xóa".
        clonedRow.find('.add-btn')
            .removeClass('btn-success add-btn')
            .addClass('btn-danger remove-btn')
            .attr('onclick', 'removeQuestionFromTable(this)')
            .text('Xóa');

        // Thêm hàng đã clone vào bảng question-table.
        $('#question-table tbody').append(clonedRow);
    }

    function removeQuestionFromTable(button) {
        // Xóa hàng tương ứng khỏi bảng question-table.
        $(button).closest('tr').remove();
    }

    $('#exit-add-questions').on('click', function() {
        if (confirm("Bạn có chắc muốn thoát? Dữ liệu sẽ bị mất.")) {

             // Redirect  về trang  danh sách đề thi  hoặc xử lý  thoát theo  ý của bạn
            window.location.href = "PExamManagement.php"; // Example
       }
    });

    $(document).ready(function() {
        $('#save-exam-questions').on('click', function() {
            var examId = $('#examId').val(); // Lấy examId từ trường input
            var questionIds = [];

            $("#question-table tbody tr").each(function() {
                questionIds.push($(this).find("td:eq(0)").text().trim());
            });

            if (questionIds.length === 0) {
                return alert("Chưa có câu hỏi nào trong đề.");
            }

            const urlEncodedDataPairs = [];
            for (const [name, value] of Object.entries({ examId, questionIds })) {
                if (Array.isArray(value)) {
                    for (const item of value) {
                        urlEncodedDataPairs.push(`${encodeURIComponent(name)}[]=${encodeURIComponent(item)}`);
                    }
                } else {
                    urlEncodedDataPairs.push(`${encodeURIComponent(name)}=${encodeURIComponent(value)}`);
                }
            }
            const urlEncodedData = urlEncodedDataPairs.join('&');

            // Xóa dữ liệu cũ
            fetch(`../conn/delete_old_exam_ques.php?examId=${encodeURIComponent(examId)}`)
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'deleted') {
                    // Lưu dữ liệu mới
                    saveNewQuestions(urlEncodedData);
                } else {
                    saveNewQuestions(urlEncodedData);
                }
            })
            .catch((error) => {
                console.error('Lỗi Fetch:', error);
                alert('Lỗi kết nối đến máy chủ.');
            });
        });

        function saveNewQuestions(urlEncodedData) {
            fetch('../conn/save_exam_question.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: urlEncodedData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    alert('Lưu câu hỏi thành công!');
                    window.location.href = "../PageAdmin/PExamManagement.php"; // Ví dụ
                } else {
                    console.error("Server response:", data);
                    alert('Lỗi khi lưu câu hỏi: ' + data);
                }
            })
            .catch((error) => {
                console.error('Lỗi Fetch:', error);
                alert('Lỗi kết nối đến máy chủ.');
            });
        }

        function addQuestionToExam(quesId) {
            var maxQuestions = parseInt($('#numOfQues').val());
            var currentCount = $('#question-table tbody tr').length;

            if (currentCount >= maxQuestions) {
                alert('Bạn chỉ được phép thêm tối đa ' + maxQuestions + ' câu hỏi.');
                return;
            }

            var row = $('#search-question-table').find('tr:has(button[onclick*="' + quesId + '"])');

            if ($('#question-table').find('tr:has(td:contains("' + quesId + '"))').length > 0) {
                alert('Câu hỏi này đã được thêm!');
                return;
            }

            var clonedRow = row.clone();

            clonedRow.find('.add-btn')
                .removeClass('btn-success add-btn')
                .addClass('btn-danger remove-btn')
                .attr('onclick', 'removeQuestionFromTable(this)')
                .text('Xóa');

            $('#question-table tbody').append(clonedRow);

            // Remove the "Chưa có câu hỏi nào trong đề thi." row if it exists
            $('#question-table tbody tr:contains("Chưa có câu hỏi nào trong đề thi.")').remove();
        }
    });
   </script>
</body>
</html>
<?php
include('../template/Tfooter.php');
?>