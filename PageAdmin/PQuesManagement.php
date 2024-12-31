<?php
// Adjust these paths to match your directory structure:
include('../conn/conn_database.php');
include('../template/Tmenubar.php');
session_start();

// Function to generate QuesId with format: Q{Class}-{increment} (unique with each entry)
function generateQuesId($conn) {
    do {
        $randomNumber = rand(1000, 9999);
        $quesId = 'QUES' . $randomNumber;
        $sql_check = "SELECT QuesId FROM questions WHERE QuesId = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $quesId);
        $stmt_check->execute();
        $stmt_check->store_result();
    } while ($stmt_check->num_rows > 0);
    return $quesId;
}


// Xử lý khi form được submit
if (isset($_POST['save'])) {

    // Retrieve variables same structure like previous example
    $quesId = $_POST['quesId'] ?? null; // ID câu hỏi, nếu có
    $question = $_POST['question'] ?? null;
    $answer1 = $_POST['answer1'] ?? null;
    $answer2 = $_POST['answer2'] ?? null;
    $answer3 = $_POST['answer3'] ?? null;
    $answer4 = $_POST['answer4'] ?? null;
    $correctAnswer = $_POST['correct-answer'] ?? null;
    $subject = $_POST['subject'] ?? null;
    $selectedClass = $_POST['class'] ?? null;
    $difficulty = $_POST['difficulty'] ?? null;

    // Kiểm tra nếu các trường bắt buộc không rỗng
    if (empty($question) || empty($answer1) || empty($answer2) || empty($correctAnswer) || empty($subject) || empty($selectedClass) || empty($difficulty)) {
        echo "<script> alert('Vui lòng nhập đủ thông tin, các trường bắt buộc không được để trống') </script>";
    } else {
        if (!empty($answer1) && !empty($answer2)) {
            if ($quesId) {
                // Nếu QuesId đã có, thực hiện cập nhật câu hỏi
                $sql = "UPDATE Questions 
                        SET QuesTitle = '$question', 
                            Difficult = '$difficulty', 
                            Answer1 = '$answer1', 
                            Answer2 = '$answer2', 
                            Answer3 = '$answer3', 
                            Answer4 = '$answer4', 
                            Correct = '$correctAnswer', 
                            Class = '$selectedClass', 
                            Subject = '$subject' 
                        WHERE QuesId = '$quesId'";

                if ($conn->query($sql) === TRUE) {
                    echo "<script> alert('Câu hỏi đã được cập nhật thành công!') </script>";
                } else {
                    echo "Lỗi: " . $sql . "<br>" . $conn->error;
                }
            } else {
                // Nếu QuesId không có (là câu hỏi mới), thực hiện tạo ID mới và INSERT câu hỏi
                $quesId = generateQuesId($conn); // Tạo QuesId mới

                // Kiểm tra nếu QuesId đã tồn tại trước khi thực hiện INSERT
                $checkSql = "SELECT COUNT(*) FROM Questions WHERE QuesId = '$quesId'";
                $result = $conn->query($checkSql);
                $count = $result->fetch_row()[0];

                if ($count == 0) {
                    // Nếu QuesId chưa tồn tại, thực hiện INSERT câu hỏi mới
                    $sql = "INSERT INTO Questions (QuesId, QuesTitle, Difficult, Answer1, Answer2, Answer3, Answer4, Correct, Class, Subject)
                            VALUES ('$quesId', '$question', '$difficulty', '$answer1', '$answer2', '$answer3', '$answer4', '$correctAnswer', '$selectedClass', '$subject')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<script> alert('Câu hỏi đã được lưu thành công!') </script>";
                    } else {
                        echo "Lỗi: " . $sql . "<br>" . $conn->error;
                    }
                } else {
                    // Nếu QuesId đã tồn tại, không thực hiện INSERT
                    echo "<script> alert('QuesId này đã tồn tại. Không thể thêm câu hỏi mới.') </script>";
                }
            }
        } else {
            echo "<script> alert('Đáp án 1 và 2 không được bỏ trống') </script>";
        }
    }
}


// Xử lý phân trang, set similar functionality for limit, page calculation with offset value in sql parameter to render correct pages with data using `LIMIT... OFFSET` clause as sql variable to change values

   $limit = 15; // page limitation with max number return values of records when db operation occurs when reloaded. (static number for simplicity purposes). set in variables for usage

 $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // from browser input in URL parameters during page render (number set as integer ) to be used. If nothing then defaults 1 to start pages
 $offset = ($page - 1) * $limit;

 $sqlCount = "SELECT count(*) AS total FROM Questions";
   $count = $conn->query($sqlCount);

 $totalPages = 1;
  if ($count && $count->num_rows > 0) {
  $row = $count->fetch_assoc(); // convert results as string associated index for easier usage
     $totalPages = ceil((int)$row["total"] / $limit);
} else{ // added a proper check if results is returned (otherwise default). You can add logging if its zero db count to find why its empty.
         error_log('No value of database when calling database to list pages or connection issue');
     }
     $sql = "SELECT * from Questions ORDER by QuesId DESC LIMIT $limit OFFSET $offset";
     $result = $conn->query($sql);
     
     ?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Ngân Hàng Câu Hỏi</title>
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
               tr {
                text-align: center;
               }
         </style>
  </head>
 <body>
    <header class="bg-primary text-white text-center py-3">
         <h1>Ngân Hàng Câu Hỏi</h1>
    </header>
        <div class="container mt-4">
         <h2 class="text-primary">Danh Sách Câu Hỏi</h2>

        <div class="add-question text-right mb-3">
           <button class="btn btn-primary" onclick="toggleForm()">Thêm Câu Hỏi Mới</button>
        </div>
           <div id="question-form" class="p-4 mb-3 bg-light border rounded" style="display: none;">
               <h3 class="text-primary">Thêm/Sửa Câu Hỏi</h3>
           <form id="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> <!-- action for posting form element when form tag attribute pressed/triggered-->
           <div class="form-group">
                        <label for="question">ID câu hỏi:</label>
                       <input type="text" class="form-control" id="quesId" name="quesId" readonly>
              </div>
           <div class="form-group">
                        <label for="question">Nội dung câu hỏi:</label>
                       <input type="text" class="form-control" id="question" name="question" required>
              </div>
                <div class="form-group">
                    <label for="answer-count">Số lượng đáp án:</label>
                     <select class="form-control" id="answer-count" >  <!--  jquery sets range of values based on user interaction selection based on change -->
                        <option value="2">2</option>
                      <option value="3">3</option>
                    <option value="4">4</option>
                  </select>
              </div>
               <div class="form-group">
                   <label for="answer1">Đáp án 1:</label>
                  <input type="text" class="form-control" id="answer1" name="answer1" required>
                   </div>
                 <div class="form-group">
                     <label for="answer2">Đáp án 2:</label>
                      <input type="text" class="form-control" id="answer2" name="answer2" required>
                    </div>
                <div class="form-group"  id="answer3-form" >
                     <label for="answer3" >Đáp án 3:</label>
                       <input type="text" class="form-control" id="answer3" name="answer3"  >
                   </div>
                    <div class="form-group" id="answer4-form">
                     <label for="answer4">Đáp án 4:</label>
                         <input type="text" class="form-control" id="answer4" name="answer4" >
                     </div>
                     <div class="form-group correct-answers-wrapper">  <!-- wrapper div, position container -->
                      <label for="correct-answer">Đáp án đúng:</label>
                        <select  class="form-control" id="correct-answer" name="correct-answer" required>
                                    <option value="1">Đáp án 1</option>
                                     <option value="2">Đáp án 2</option>
                             <option value="3" class="answer-option" >Đáp án 3</option>  <!-- add class as html attr -->
                              <option value="4"   class="answer-option"  >Đáp án 4</option>
                         </select>
                     </div>
                 <div class="form-group">
                    <label for="subject">Môn học:</label>
                  <select  class="form-control" id="subject" name="subject" required>
                   <option value="Toán">Toán</option>
                   <option value="Lý">Lý</option>
                  <option value="Hóa">Hóa</option>
                    <option value="Sinh">Sinh</option>
                </select>
                  </div>
             <div class="form-group">
                     <label for="class">Lớp:</label>
                <select class="form-control" id="class" name="class" required>
               <option value="10">10</option>
                <option value="11">11</option>
                 <option value="12">12</option>
             </select>
         </div>
            <div class="form-group">
                    <label for="difficulty">Độ khó:</label>
                       <select  class="form-control"  id="difficulty" name="difficulty" required>
                                 <option value="Dễ">Dễ</option>
                          <option value="Trung Bình">Trung Bình</option>
                         <option value="Khó">Khó</option>
                   </select>
         </div>
       <button type="submit" name="save" class="btn btn-primary">Lưu</button> <!-- all name properties passed in all tags inside this tag "form" tag will send that value when this button pressed  -->

          </form>
      </div>
      <table class="table question-table table-bordered" id="question-table">
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
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
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
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary edit-btn" onclick="editQuestion(this)">Sửa</button>
                            <button class="btn btn-sm btn-danger delete-btn" onclick="deleteQuestion(this)">Xóa</button>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center">Không có dữ liệu từ danh sách</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>


    <!-- next/previous page link and set html style as bootstrap style buttons and text  -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . ($page - 1) ?>" aria-label="Previous">
                        <span aria-hidden="true"> Trước </span>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link"> Trước </span>
                </li>
            <?php endif; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $_SERVER['PHP_SELF'] . '?page=' . ($page + 1) ?>" aria-label="Next">
                        <span aria-hidden="true"> Sau </span>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link"> Sau </span>
                </li>
            <?php endif; ?>
        </ul>
        <?php if ($totalPages < $page): ?>
            <div style="text-align:center"> Không có thêm dữ liệu, tổng số trang: <?php echo htmlspecialchars($totalPages); ?> </div>
        <?php endif; ?>
    </nav>

    <!-- Footer using regular Html properties for simplicity to maintain visual uniformity -->
    <footer class="text-center py-4">
        <p>© Nhóm 6 - Website Tạo Đề Thi Trắc Nghiệm</p>
    </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#answer-count').on('change', function() {
                var count = parseInt($(this).val());
                if (count >= 3) {
                    $("#answer3-form").removeClass('hidden');
                } else {
                    $("#answer3-form").addClass('hidden');
                }
                if (count == 4) {
                    $("#answer4-form").removeClass('hidden');
                } else {
                    $("#answer4-form").addClass('hidden');
                }
                $('#correct-answer').empty();
                for (var i = 1; i <= count; i++) {
                    $('#correct-answer').append(`<option value="${i}">Đáp án ${i}</option>`);
                }
            });

            $('#answer-count').trigger('change');
        });

        function toggleForm() {
            const form = document.getElementById('question-form');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        function saveQuestion() {
            if (confirm('Bạn có chắc chắn muốn lưu không ?')) {
                return true;
            }
            return false;
        }

        function editQuestion(button) {
            const row = $(button).closest('tr');
            
            // Lấy giá trị của QuesId từ một ô trong bảng (giả sử là cột đầu tiên)
            const quesId = row.find('td:eq(0)').text().trim();  // Chỉnh lại index nếu QuesId nằm ở cột khác
            
            // Xuất QuesId vào console (hoặc làm gì đó với nó)
            console.log("QuesId: " + quesId);
            
            // Đặt QuesId vào một input ẩn để gửi về server nếu cần
            $('#quesId').val(quesId);
            
            // Tiến hành gán các giá trị khác như bình thường
            $('#question').val(row.find('td:eq(1)').text());
            const answers = row.find('td:eq(2)').text().split('\n').map(line => line.replace('• ', '').trim()).filter(Boolean);
            $('#answer1').val(answers[0] || '');
            $('#answer2').val(answers[1] || '');
            $('#answer3').val(answers[2] || '');
            $('#answer4').val(answers[3] || '');
            const correctAnswer = ['A', 'B', 'C', 'D'].indexOf(row.find('td:eq(3)').text().trim()) + 1;
            $('#correct-answer').val(correctAnswer);
            $('#subject').val(row.find('td:eq(4)').text().trim());
            $('#class').val(row.find('td:eq(5)').text().trim());
            $('#difficulty').val(row.find('td:eq(6)').text().trim());
            const answerNumberFromTable = row.find('td:eq(2)').text().trim().split("\n").filter(item => item !== '').length;
            $("#answer-count").val(answerNumberFromTable.toString());
            $('#answer-count').trigger('change');
            toggleForm();
            row.remove();
        }


        function deleteQuestion(button) {
            if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
                const row = $(button).closest('tr'); // Hàng trong table
                const quesId = row.find('td:eq(0)').text().trim();

                // Xóa hàng khỏi giao diện ngay lập tức
                const row1 = button.parentElement.parentElement.parentElement;
                row1.remove();

                // Gửi yêu cầu đến server
                fetch('../conn/delete_question.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `delete_question=true&QuesIdToDelete=${encodeURIComponent(quesId)}`
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Response từ server:', data);
                    if (data.trim() === 'success') {
                        alert('Câu hỏi đã được xóa thành công!');
                    } else {
                        alert('Lỗi khi xóa câu hỏi: ' + data);
                        // Phục hồi hàng trong giao diện nếu có lỗi
                        $('table').append(row);
                    }
                })
                .catch(error => {
                    console.error('Lỗi kết nối đến máy chủ:', error);
                    alert('Lỗi kết nối đến máy chủ.');
                    // Phục hồi hàng trong giao diện nếu có lỗi
                    $('table').append(row);
                });
            }
        }


   </script>

</body>
</html>
<?php
include('../template/Tfooter.php');
$conn->close();
?>