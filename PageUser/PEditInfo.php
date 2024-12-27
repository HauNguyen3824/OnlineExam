<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PEditInfo.php
session_start();
include('../conn/conn_database.php');

// Lấy userId từ URL
$userId = $_GET['userId'] ?? null;

if (!$userId) {
    die("Thiếu thông tin người dùng.");
}

// Truy vấn thông tin người dùng từ cơ sở dữ liệu
$sql_user = "SELECT userId, fullName, email, phone, class, year FROM Users WHERE userId = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("s", $userId);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows === 0) {
    die("Không tìm thấy thông tin người dùng.");
}

$user = $result_user->fetch_assoc();

// Xử lý form khi người dùng nhấn nút "Lưu"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Cập nhật thông tin người dùng trong cơ sở dữ liệu
    $sql_update = "UPDATE Users SET email = ?, phone = ? WHERE userId = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sss", $email, $phone, $userId);
    $stmt_update->execute();

    // Chuyển hướng về trang thông tin người dùng
    header("Location: PProfileUser.php?userId=" . urlencode($userId));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin người dùng</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Thông tin người dùng</h1>
    </header>
    <div class="container">
        <form method="post">
            <div class="form-group">
                <label for="userId">UserId</label>
                <input type="text" class="form-control" id="userId" name="userId" value="<?= htmlspecialchars($user['userId']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" class="form-control" id="fullName" name="fullName" value="<?= htmlspecialchars($user['fullName']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="class">Class</label>
                <input type="text" class="form-control" id="class" name="class" value="<?= htmlspecialchars($user['class']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="year">Year</label>
                <input type="text" class="form-control" id="year" name="year" value="<?= htmlspecialchars($user['year']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            </div>
            <div class="btn-container mt-4">
                <button type="button" class="btn btn-secondary" onclick="confirmBack()">Quay lại</button>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Bạn có chắc chắn với lựa chọn này?')">Lưu</button>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmBack() {
            if (confirm('Bạn có chắc muốn quay lại?')) {
                window.location.href = 'PProfileUser.php?userId=<?= htmlspecialchars($userId) ?>';
            }
        }

        function validateForm() {
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const emailPattern = /@gmail\.com$/;
            const phonePattern = /^\d{10}$/;

            if (!emailPattern.test(email)) {
                alert('Email phải có đuôi @gmail.com.');
                return false;
            }

            if (!phonePattern.test(phone)) {
                alert('Phone phải có 10 số.');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
<?php
$stmt_user->close();
include('../template/Tfooter.php');
$conn->close();
?>