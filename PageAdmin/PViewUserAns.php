<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PViewUserAns.php
session_start();
include('../conn/conn_database.php');

// Lấy examId và userId từ URL
$examId = $_GET['examId'] ?? null;
$userId = $_GET['userId'] ?? null;

if (!$examId || !$userId) {
    die("Thiếu thông tin đề thi hoặc người dùng.");
}

// Truy vấn bảng adduser để lấy auid
$sql_auid = "SELECT AUId FROM adduser WHERE examId = ? AND userId = ?";
$stmt_auid = $conn->prepare($sql_auid);
$stmt_auid->bind_param("ss", $examId, $userId);
$stmt_auid->execute();
$result_auid = $stmt_auid->get_result();

if ($result_auid->num_rows === 0) {
    die("Không tìm thấy thông tin.");
}

$auid = $result_auid->fetch_assoc()['AUId'];

// Truy vấn bảng exams để lấy thông tin đề thi
$sql_exam = "SELECT examId, examTitle, duration, numOfQues, subject, difficult FROM exams WHERE examId = ?";
$stmt_exam = $conn->prepare($sql_exam);
$stmt_exam->bind_param("s", $examId);
$stmt_exam->execute();
$result_exam = $stmt_exam->get_result();

if ($result_exam->num_rows === 0) {
    die("Không tìm thấy thông tin đề thi.");
}

$exam = $result_exam->fetch_assoc();

// Truy vấn bảng Users để lấy thông tin người dùng
$sql_user = "SELECT userId, fullName FROM Users WHERE userId = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $userId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    die("Không tìm thấy thông tin người dùng.");
}

$user = $result_user->fetch_assoc();

// Truy vấn bảng results để lấy thông tin kết quả
$sql_result = "SELECT Score, TimeStart, TimeSubmit FROM results WHERE AUId = ?";
$stmt_result = $conn->prepare($sql_result);
$stmt_result->bind_param("s", $auid);
$stmt_result->execute();
$result_result = $stmt_result->get_result();

if ($result_result->num_rows === 0) {
    die("Không tìm thấy thông tin kết quả.");
}

$result = $result_result->fetch_assoc();

// Truy vấn bảng useranswers để lấy thông tin đáp án
$sql_answers = "SELECT UserChoice, IsCorrect FROM useranswers WHERE AUId = ?";
$stmt_answers = $conn->prepare($sql_answers);
$stmt_answers->bind_param("s", $auid);
$stmt_answers->execute();
$result_answers = $stmt_answers->get_result();

if ($result_answers === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}

// Đếm số đáp án đúng và sai
$correctCount = 0;
$incorrectCount = 0;
$choices = [0, 0, 0, 0, 0]; // Khởi tạo mảng để đếm số lượng lựa chọn 0, 1, 2, 3, 4
while ($row = $result_answers->fetch_assoc()) {
    if ($row['IsCorrect'] == 1) {
        $correctCount++;
    } else {
        $incorrectCount++;
    }
    $choices[$row['UserChoice']]++; // Đếm số lượng mỗi lựa chọn
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem lựa chọn của người dùng</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #resultChart, #choiceChart {
            width: 100%;
            height: 300px;
            max-width: 600px;
            max-height: 300px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php include '../template/Tmenubar.php'; ?>
    <header class="bg-primary text-white text-center py-3">
        <h1>Lựa chọn của người dùng</h1>
    </header>
    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Thông tin đề thi</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="examId">ExamId</label>
                            <input type="text" class="form-control" id="examId" name="examId" value="<?= htmlspecialchars($exam['examId']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="examTitle">ExamTitle</label>
                            <input type="text" class="form-control" id="examTitle" name="examTitle" value="<?= htmlspecialchars($exam['examTitle']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="duration">Duration</label>
                            <input type="text" class="form-control" id="duration" name="duration" value="<?= htmlspecialchars($exam['duration']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="numOfQues">NumOfQues</label>
                            <input type="text" class="form-control" id="numOfQues" name="numOfQues" value="<?= htmlspecialchars($exam['numOfQues']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="<?= htmlspecialchars($exam['subject']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="difficult">Difficult</label>
                            <input type="text" class="form-control" id="difficult" name="difficult" value="<?= htmlspecialchars($exam['difficult']) ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2>Thông tin người dùng</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="userId">UserId</label>
                            <input type="text" class="form-control" id="userId" name="userId" value="<?= htmlspecialchars($user['userId']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" value="<?= htmlspecialchars($user['fullName']) ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header">
                        <h2>Thông tin kết quả</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="score">Score</label>
                            <input type="text" class="form-control" id="score" name="score" value="<?= htmlspecialchars($result['Score']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="timeStart">Time Start</label>
                            <input type="text" class="form-control" id="timeStart" name="timeStart" value="<?= htmlspecialchars($result['TimeStart']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="timeSubmit">Time Submit</label>
                            <input type="text" class="form-control" id="timeSubmit" name="timeSubmit" value="<?= htmlspecialchars($result['TimeSubmit']) ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Biểu đồ tròn thể hiện tỉ lệ đúng/sai</h3>
                <canvas id="resultChart"></canvas>
            </div>
            <div class="col-md-6">
                <h3>Biểu đồ cột thể hiện phân bố lựa chọn</h3>
                <canvas id="choiceChart"></canvas>
            </div>
        </div>
        <h2 class="mt-4">Thông tin đáp án</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>UserChoice</th>
                    <th>IsCorrect</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Reset the result set pointer and fetch data again for the table
                $result_answers->data_seek(0);
                while ($row = $result_answers->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['UserChoice']) ?></td>
                        <td><?= htmlspecialchars($row['IsCorrect']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="text-center mt-4">
        <button type="button" class="btn btn-secondary" onclick="confirmBack()">Quay lại</button>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmBack() {
            if (confirm('Bạn có chắc muốn quay lại?')) {
                window.location.href = 'PViewResults.php?examId=<?= htmlspecialchars($examId) ?>';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Biểu đồ tròn
            const ctxPie = document.getElementById('resultChart').getContext('2d');
            const resultChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: ['Đúng', 'Sai'],
                    datasets: [{
                        data: [<?= $correctCount ?>, <?= $incorrectCount ?>],
                        backgroundColor: ['#28a745', '#dc3545'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Biểu đồ cột
            const ctxBar = document.getElementById('choiceChart').getContext('2d');
            const choiceChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['0', '1', '2', '3', '4'],
                    datasets: [{
                        label: 'Lựa chọn của người dùng',
                        data: [<?= implode(',', $choices) ?>],
                        backgroundColor: '#007bff',
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Lựa chọn'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Số lượng'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
<?php
include('../template/Tfooter.php');
$stmt_auid->close();
$stmt_exam->close();
$stmt_user->close();
$stmt_result->close();
$stmt_answers->close();
$conn->close();
?>