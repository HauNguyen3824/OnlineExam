<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PUserManagement.php
session_start();
include('../conn/conn_database.php');

// Lấy các giá trị từ form
$classFilter = $_GET['class'] ?? '';
$yearFilter = $_GET['year'] ?? '';
$fullNameFilter = $_GET['fullName'] ?? '';
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Truy vấn danh sách người dùng có role là user
$sql_users = "SELECT userId, fullName, email, phone, class, year FROM Users WHERE role = 'user'";

// Thêm điều kiện lọc nếu có
$conditions = [];
$params = [];
$types = '';

if ($classFilter) {
    $conditions[] = "class = ?";
    $params[] = $classFilter;
    $types .= 's';
}

if ($yearFilter) {
    $conditions[] = "year = ?";
    $params[] = $yearFilter;
    $types .= 's';
}

if ($fullNameFilter) {
    $conditions[] = "fullName LIKE ?";
    $params[] = '%' . $fullNameFilter . '%';
    $types .= 's';
}

if ($conditions) {
    $sql_users .= ' AND ' . implode(' AND ', $conditions);
}

// Thêm phân trang
$sql_users .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt_users = $conn->prepare($sql_users);
if ($params) {
    $stmt_users->bind_param($types, ...$params);
}
$stmt_users->execute();
$result_users = $stmt_users->get_result();

if ($result_users === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}

// Truy vấn để lấy tổng số người dùng có role là user
$sql_count = "SELECT COUNT(*) as total FROM Users WHERE role = 'user'";
if ($conditions) {
    $sql_count .= ' AND ' . implode(' AND ', $conditions);
}
$stmt_count = $conn->prepare($sql_count);
if ($params) {
    $count_params = array_slice($params, 0, -2); // Bỏ qua LIMIT và OFFSET
    $count_types = substr($types, 0, -2); // Remove the last two characters ('ii')
    if (!empty($count_types)) {
        $stmt_count->bind_param($count_types, ...$count_params);
    }
}
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_users = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

// Truy vấn để lấy các giá trị duy nhất của cột Year
$sql_years = "SELECT DISTINCT year FROM Users WHERE role = 'user'";
$result_years = $conn->query($sql_years);

if ($result_years === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}

// Xử lý đặt lại mật khẩu mặc định
if (isset($_POST['resetPassword'])) {
    $userId = $_POST['userId'];
    $defaultPassword = substr($userId, -3); // Lấy 3 số cuối của userId làm mật khẩu mặc định
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT); // Mã hóa mật khẩu

    $sql_reset_password = "UPDATE Users SET password = ? WHERE userId = ?";
    $stmt_reset_password = $conn->prepare($sql_reset_password);
    $stmt_reset_password->bind_param("ss", $hashedPassword, $userId);
    $stmt_reset_password->execute();

    echo "<script>alert('Mật khẩu của người dùng $userId đã được đặt lại thành $defaultPassword');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        tr {
            text-align: center;
        }
    </style>
    <script>
        function confirmReset(userId) {
            if (confirm('Bạn có chắc muốn đặt lại mật khẩu mặc định cho userId ' + userId + ' không?')) {
                document.getElementById('resetPasswordForm-' + userId).submit();
            }
        }
    </script>
</head>
<body>
    <?php include '../template/Tmenubar.php'; ?>
    <header class="bg-primary text-white text-center py-3">
        <h1>Quản Lý Người Dùng</h1>
    </header>
    <div class="container mt-4">
        <form method="get" class="form-inline mb-4">
            <div class="form-group mr-2">
                <label for="class" class="mr-2">Lớp</label>
                <select name="class" id="class" class="form-control">
                    <option value="">Tất cả</option>
                    <option value="10" <?= $classFilter == '10' ? 'selected' : '' ?>>10</option>
                    <option value="11" <?= $classFilter == '11' ? 'selected' : '' ?>>11</option>
                    <option value="12" <?= $classFilter == '12' ? 'selected' : '' ?>>12</option>
                </select>
            </div>
            <div class="form-group mr-2">
                <label for="year" class="mr-2">Year</label>
                <select name="year" id="year" class="form-control">
                    <option value="">Tất cả</option>
                    <?php while ($row = $result_years->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['year']) ?>" <?= $yearFilter == $row['year'] ? 'selected' : '' ?>><?= htmlspecialchars($row['year']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group mr-2">
                <label for="fullName" class="mr-2">Full Name</label>
                <input type="text" name="fullName" id="fullName" class="form-control" value="<?= htmlspecialchars($fullNameFilter) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
        </form>
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
                    <?php while ($row = $result_users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['userId']) ?></td>
                            <td><?= htmlspecialchars($row['fullName']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['class']) ?></td>
                            <td><?= htmlspecialchars($row['year']) ?></td>
                            <td>
                                <a href="PEditUserInfo.php?userId=<?= htmlspecialchars($row['userId']) ?>" class="btn btn-primary">Sửa thông tin</a>
                                <a href="PViewUserExams.php?userId=<?= htmlspecialchars($row['userId']) ?>" class="btn btn-warning">Xem các bài làm</a>
                                <form id="resetPasswordForm-<?= htmlspecialchars($row['userId']) ?>" method="post" style="display:inline;">
                                    <input type="hidden" name="userId" value="<?= htmlspecialchars($row['userId']) ?>">
                                    <input type="hidden" name="resetPassword" value="1">
                                    <button type="button" class="btn btn-danger" onclick="confirmReset('<?= htmlspecialchars($row['userId']) ?>')">Đặt mật khẩu mặc định</button>
                                </form>
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
        <div class="d-flex justify-content-between">
            <a href="?page=<?= max(1, $page - 1) ?>&class=<?= htmlspecialchars($classFilter) ?>&year=<?= htmlspecialchars($yearFilter) ?>&fullName=<?= htmlspecialchars($fullNameFilter) ?>" class="btn btn-primary <?= $page <= 1 ? 'disabled' : '' ?>">Trước</a>
            <span>Trang <?= $page ?> / <?= $total_pages ?></span>
            <a href="?page=<?= min($total_pages, $page + 1) ?>&class=<?= htmlspecialchars($classFilter) ?>&year=<?= htmlspecialchars($yearFilter) ?>&fullName=<?= htmlspecialchars($fullNameFilter) ?>" class="btn btn-primary <?= $page >= $total_pages ? 'disabled' : '' ?>">Sau</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
include('../template/Tfooter.php');
$stmt_users->close();
$conn->close();
?>