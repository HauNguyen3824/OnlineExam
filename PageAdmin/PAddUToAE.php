<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PAddUToAE.php
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

// Truy vấn tất cả người dùng từ bảng Users
$sql_users = "SELECT * FROM Users";
$result_users = $conn->query($sql_users);

if (!$result_users) {
    die("Lỗi truy vấn người dùng: " . $conn->error);
}

// Lấy danh sách UserId từ bảng AddUsers
$sql_exam_users = "SELECT UserId FROM AddUser WHERE ExamId = ?";
$stmt_exam_users = $conn->prepare($sql_exam_users);
if ($stmt_exam_users) {
    $stmt_exam_users->bind_param("s", $examId);
    $stmt_exam_users->execute();
    $result_exam_users = $stmt_exam_users->get_result();
    $exam_users = [];
    while ($row = $result_exam_users->fetch_assoc()) {
        $exam_users[] = $row['UserId'];
    }
    $stmt_exam_users->close();
} else {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng Vào Đề</title>
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
        .table-wrapper {
            width: 48%; /* Adjust width as needed */
            /* Add other styles like borders or margins */
        }
        .nav-item {
            text-align: center;
            padding-left: 3%;
        }
    </style>
</head>
<body>
<header class="bg-primary text-white text-center py-3">
    <h1>Thêm Người Dùng Vào Đề</h1>
</header>
<div class="container mt-4">
    <h2 class="text-primary">Thông tin đề</h2>
    <form id="form" method="post" action="PAddUToAE.php">
        <div class="row mb-3">
            <div class="col-md-2">
                <label for="examId" class="form-label">Exam ID</label>
                <input type="text" id="examId" name="examId" class="form-control" value="<?php echo htmlspecialchars($examId); ?>" readonly>
            </div>
            <div class="col-md-3">
                <label for="examTitle" class="form-label">Exam Title</label>
                <input type="text" id="examTitle" name="examTitle" class="form-control" value="<?php echo htmlspecialchars($examTitle); ?>" readonly>
            </div>
            <div class="col-md-2">
                <label for="duration" class="form-label">Duration</label>
                <input type="text" id="duration" name="duration" class="form-control" value="<?php echo htmlspecialchars($duration); ?>" readonly>
            </div>
            <div class="col-md-2">
                <label for="numOfQues" class="form-label">Num of Ques</label>
                <input type="text" id="numOfQues" name="numOfQues" class="form-control" value="<?php echo htmlspecialchars($numOfQues); ?>" readonly>
            </div>
            <div class="col-md-2">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject); ?>" readonly>
            </div>
            <div class="col-md-1">
                <label for="difficult" class="form-label">Difficult</label>
                <input type="text" id="difficult" name="difficult" class="form-control" value="<?php echo htmlspecialchars($difficult); ?>" readonly>
            </div>
        </div>
    </form>
</div>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 table-wrapper">
            <h2>Người dùng của đề</h2>
            <table class="table table-bordered" id="add-user-table">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>UserId</th>
                        <th>FullName</th>
                        <th>Username</th>
                        <th>Class</th>
                        <th>Year</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($exam_users)): ?>
                        <?php
                        $userId_string = "'" . implode("','", $exam_users) . "'";
                        $sql_user_details = "SELECT * FROM Users WHERE UserId IN ($userId_string)";
                        $result_user_details = $conn->query($sql_user_details);

                        if ($result_user_details && $result_user_details->num_rows > 0):
                            while ($row = $result_user_details->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["UserId"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["FullName"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["Username"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["Class"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["Year"]); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger remove-btn" onclick="removeUserFromTable(this)">Xóa</button>
                                    </td>
                                </tr>
                            <?php endwhile;
                        endif;
                        ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center">Không có người dùng nào trong đề thi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <button type="button" id="save-exam-users" class="btn btn-primary">Lưu người dùng của đề</button>
            <button type="button" id="exit-add-users" class="btn btn-secondary">Thoát</button>
        </div>

        <div class="col-md-6 table-wrapper">  <!-- Wrapper cho bảng tra cứu -->
            <h2>Tra cứu người dùng</h2>
            <table class="table table-bordered" id="search-user-table">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>UserId</th>
                        <th>FullName</th>
                        <th>Username</th>
                        <th>Class</th>
                        <th>Year</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_users && $result_users->num_rows > 0): ?>
                        <?php while ($row = $result_users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["UserId"]); ?></td>
                                <td><?php echo htmlspecialchars($row["FullName"]); ?></td>
                                <td><?php echo htmlspecialchars($row["Username"]); ?></td>
                                <td><?php echo htmlspecialchars($row["Class"]); ?></td>
                                <td><?php echo htmlspecialchars($row["Year"]); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success add-btn" onclick="addUserToExam('<?= $row['UserId'] ?>', '<?= $examId ?>')">Thêm</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center">Không có người dùng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function addUserToExam(userId, examId) {
        var row = $('#search-user-table').find('tr:has(button[onclick*="' + userId + '"])');

        if ($('#add-user-table').find('tr:has(td:contains("' + userId + '"))').length > 0) {
            alert('Người dùng này đã được thêm!');
            return;
        }

        var clonedRow = row.clone();

        clonedRow.find('.add-btn')
            .removeClass('btn-success add-btn')
            .addClass('btn-danger remove-btn')
            .attr('onclick', 'removeUserFromTable(this)')
            .text('Xóa');

        $('#add-user-table tbody').append(clonedRow);

        $('#add-user-table tbody tr:contains("Không có người dùng nào trong đề thi.")').remove();
    }

    function removeUserFromTable(button) {
        $(button).closest('tr').remove();
    }

    $('#exit-add-users').on('click', function() {
        if (confirm("Bạn có chắc muốn thoát? Dữ liệu sẽ bị mất.")) {
            window.location.href = "PExamManagement.php"; // Ví dụ
        }
    });

    $(document).ready(function() {
        $('#save-exam-users').on('click', function() {
            var examId = $('#examId').val();
            var userIds = [];

            $("#add-user-table tbody tr").each(function() {
                userIds.push($(this).find("td:eq(0)").text().trim());
            });

            if (userIds.length === 0) {
                return alert("Chưa có người dùng nào trong đề.");
            }

            const urlEncodedDataPairs = [];
            for (const [name, value] of Object.entries({ examId, userIds })) {
                if (Array.isArray(value)) {
                    for (const item of value) {
                        urlEncodedDataPairs.push(`${encodeURIComponent(name)}[]=${encodeURIComponent(item)}`);
                    }
                } else {
                    urlEncodedDataPairs.push(`${encodeURIComponent(name)}=${encodeURIComponent(value)}`);
                }
            }
            const urlEncodedData = urlEncodedDataPairs.join('&');

            if (examId) {
                // Xóa dữ liệu cũ nếu examId tồn tại
                fetch(`../conn/delete_old_exam_user.php?examId=${encodeURIComponent(examId)}`)
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'deleted') {
                        // Lưu dữ liệu mới
                        saveNewUsers(urlEncodedData);
                    } else {
                        saveNewUsers(urlEncodedData);
                    }
                })
                .catch((error) => {
                    console.error('Lỗi Fetch:', error);
                    alert('Lỗi kết nối đến máy chủ.');
                });
            } else {
                // Thực hiện thêm dữ liệu mới nếu không có examId
                saveNewUsers(urlEncodedData);
            }
        });

        function saveNewUsers(urlEncodedData) {
            fetch('../conn/save_exam_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: urlEncodedData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    alert('Lưu người dùng thành công!');
                    window.location.href = "../PageAdmin/PExamManagement.php"; // Ví dụ
                } else {
                    console.error("Server response:", data);
                    alert('Lỗi khi lưu người dùng: ' + data);
                }
            })
            .catch((error) => {
                console.error('Lỗi Fetch:', error);
                alert('Lỗi kết nối đến máy chủ.');
            });
        }
    });
</script>
</body>
</html>
<?php
include('../template/Tfooter.php');
$conn->close();
?>