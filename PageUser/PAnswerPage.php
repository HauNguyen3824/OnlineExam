<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PAnswerPage.php
session_start();
include('../conn/conn_database.php');

// Lấy examId, userId và auid từ URL
$examId = $_GET['examId'] ?? null;
$userId = $_GET['userId'] ?? null;
$auid = $_GET['auid'] ?? null;

if (!$examId || !$userId || !$auid) {
    die("Thiếu thông tin cần thiết.");
}

// Truy vấn dữ liệu từ bảng useranswers
$sql_user_answers = "SELECT AnsId, UserChoice, IsCorrect, QuesId FROM useranswers WHERE AUId = ?";
$stmt_user_answers = $conn->prepare($sql_user_answers);
$stmt_user_answers->bind_param("s", $auid);
$stmt_user_answers->execute();
$result_user_answers = $stmt_user_answers->get_result();

// Đếm số đáp án đúng và sai
$correctCount = 0;
$incorrectCount = 0;
$choices = [0, 0, 0, 0, 0]; // Khởi tạo mảng để đếm số lượng lựa chọn 0, 1, 2, 3, 4
while ($row = $result_user_answers->fetch_assoc()) {
    if ($row['IsCorrect'] == 1) {
        $correctCount++;
    } else {
        $incorrectCount++;
    }
    $choices[$row['UserChoice']]++; // Đếm số lượng mỗi lựa chọn
}

// Truy vấn dữ liệu từ bảng results
$sql_results = "SELECT ResultId, AUId, Score, TimeStart, TimeSubmit FROM results WHERE AUId = ?";
$stmt_results = $conn->prepare($sql_results);
$stmt_results->bind_param("s", $auid);
$stmt_results->execute();
$result_results = $stmt_results->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả bài thi</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #resultChart {
            width: 100%;
            height: 270px;
            max-width: 600px;
            max-height: 270px;
            margin: 0 auto;
        }
        #choiceChart {
            width: 100%;
            height: 800px;
            max-width: 600px;
            max-height: 800px;
            margin: 0 auto;
        }
        tr {
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Kết quả đề thi</h1>
    </header>
    <div class="container mt-4">
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
        <div class="mt-4">
            <h2>Bài làm của bạn: </h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>AnsId</th>
                        <th>Đáp án của bạn</th>
                        <th>Đúng/Sai</th>
                        <th>QuesId</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_user_answers->num_rows > 0): ?>
                        <?php
                        // Reset the result set pointer and fetch data again for the table
                        $result_user_answers->data_seek(0);
                        while ($row = $result_user_answers->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['AnsId']) ?></td>
                                <td><?= htmlspecialchars($row['UserChoice']) ?></td>
                                <td><?= htmlspecialchars($row['IsCorrect']) ?></td>
                                <td><?= htmlspecialchars($row['QuesId']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không có dữ liệu</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-4">
            <a href="PMenuUser.php?userId=<?= htmlspecialchars($userId) ?>" class="btn btn-primary">Xác nhận</a>
        </div>
    </div>
    <footer class="text-center py-4">
        <p>&copy; Nhóm 6 - Website Tạo Đề Thi Trắc Nghiệm</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
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
$stmt_user_answers->close();
$stmt_results->close();
$conn->close();
?>