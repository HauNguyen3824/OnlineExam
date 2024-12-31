<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PViewResults.php
session_start();
include('../conn/conn_database.php');

// Lấy examId từ URL
$examId = $_GET['examId'] ?? null;

if (!$examId) {
    die("Thiếu thông tin đề thi.");
}

// Truy vấn danh sách user đã tham gia bài thi từ bảng adduser
$sql_users = "
    SELECT u.userId, u.fullName, u.email, u.phone, u.class, u.year
    FROM adduser au
    JOIN Users u ON au.userId = u.userId
    WHERE au.examId = ? AND u.role = 'user'";
$stmt_users = $conn->prepare($sql_users);
$stmt_users->bind_param("s", $examId);
$stmt_users->execute();
$result_users = $stmt_users->get_result();

if ($result_users === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}

// Truy vấn số lượng người dùng có role là user và tổng số người dùng có cùng class
$sql_class_count = "
    SELECT u.class, COUNT(*) as total
    FROM Users u
    WHERE u.role = 'user'
    GROUP BY u.class";
$result_class_count = $conn->query($sql_class_count);

if ($result_class_count === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}

$class_counts = [];
while ($row = $result_class_count->fetch_assoc()) {
    $class_counts[$row['class']] = $row['total'];
}

$class_done_counts = [];
while ($row = $result_users->fetch_assoc()) {
    if (!isset($class_done_counts[$row['class']])) {
        $class_done_counts[$row['class']] = 0;
    }
    $class_done_counts[$row['class']]++;
}

// Truy vấn bảng adduser để lấy các auid có cùng examId
$sql_auid = "SELECT AUId FROM adduser WHERE examId = ?";
$stmt_auid = $conn->prepare($sql_auid);
$stmt_auid->bind_param("s", $examId);
$stmt_auid->execute();
$result_auid = $stmt_auid->get_result();

if ($result_auid === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}

$auids = [];
while ($row = $result_auid->fetch_assoc()) {
    $auids[] = $row['AUId'];
}

// Truy vấn bảng results để lấy các điểm số (Score) của người dùng
$scores = [];
if (!empty($auids)) {
    $placeholders = implode(',', array_fill(0, count($auids), '?'));
    $sql_scores = "SELECT Score FROM results WHERE AUId IN ($placeholders)";
    $stmt_scores = $conn->prepare($sql_scores);
    $stmt_scores->bind_param(str_repeat('s', count($auids)), ...$auids);
    $stmt_scores->execute();
    $result_scores = $stmt_scores->get_result();

    if ($result_scores === false) {
        die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    while ($row = $result_scores->fetch_assoc()) {
        $scores[] = $row['Score'];
    }
}

// Đếm số lượng người dùng đạt mỗi điểm số
$score_counts = array_count_values($scores);
ksort($score_counts); // Sắp xếp theo điểm số
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem kết quả bài thi</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        #classChart, #scoreChart {
            width: 100%;
            height: 300px;
            max-width: 600px;
            max-height: 300px;
            margin: 0 auto;
        }
        tr {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../template/Tmenubar.php'; ?>
    <header class="bg-primary text-white text-center py-3">
         <h1>Quản Lý Kết Quả</h1>
    </header>
    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Biểu đồ tròn số lượng đã làm bài</h3>
                <canvas id="classChart"></canvas>
            </div>
            <div class="col-md-6">
                <h3>Biểu đồ cột thể hiện số điểm</h3>
                <canvas id="scoreChart"></canvas>
            </div>
        </div>
        <h1 class="text-center">Danh sách người dùng đã tham gia bài thi</h1>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>UserId</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Lớp</th>
                    <th>Năm nhập học</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_users->num_rows > 0): ?>
                    <?php
                    // Reset the result set pointer and fetch data again for the table
                    $result_users->data_seek(0);
                    while ($row = $result_users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['userId']) ?></td>
                            <td><?= htmlspecialchars($row['fullName']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['class']) ?></td>
                            <td><?= htmlspecialchars($row['year']) ?></td>
                            <td>
                                <a href="PViewUserAns.php?userId=<?= htmlspecialchars($row['userId']) ?>&examId=<?= htmlspecialchars($examId) ?>" class="btn btn-primary">Xem kết quả</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Không có dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const classCounts = <?= json_encode($class_counts) ?>;
            const classDoneCounts = <?= json_encode($class_done_counts) ?>;
            const labels = Object.keys(classCounts);
            const doneData = labels.map(label => classDoneCounts[label] || 0);
            const notDoneData = labels.map(label => classCounts[label] - (classDoneCounts[label] || 0));

            const ctxClass = document.getElementById('classChart').getContext('2d');
            const classChart = new Chart(ctxClass, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Đã làm bài',
                        data: doneData,
                        backgroundColor: '#28a745',
                    }, {
                        label: 'Chưa làm bài',
                        data: notDoneData,
                        backgroundColor: '#dc3545',
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
            const scoreLabels = <?= json_encode(array_keys($score_counts)) ?>;
            const scoreData = <?= json_encode(array_values($score_counts)) ?>;
            const ctxScore = document.getElementById('scoreChart').getContext('2d');
            const scoreChart = new Chart(ctxScore, {
                type: 'bar',
                data: {
                    labels: scoreLabels,
                    datasets: [{
                        label: 'Phân bố điểm',
                        data: scoreData,
                        backgroundColor: '#007bff',
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Điểm'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Số lượng'
                            },
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1 // Chỉ hiện số nguyên
                            }
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
$stmt_users->close();
$stmt_auid->close();
$conn->close();
?>