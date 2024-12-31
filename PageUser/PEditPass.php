<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PEditPass.php
session_start();
include('../conn/conn_database.php');

// Lấy userId từ URL
$userId = $_GET['userId'] ?? null;

if (!$userId) {
    die("Thiếu thông tin người dùng.");
}

$error = '';

// Xử lý form khi người dùng nhấn nút "Lưu"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Truy vấn mật khẩu hiện tại từ cơ sở dữ liệu
    $sql_user = "SELECT password FROM Users WHERE userId = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $userId);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows === 0) {
        die("Không tìm thấy thông tin người dùng.");
    }

    $user = $result_user->fetch_assoc();
    $hashedCurrentPassword = $user['password'];

    // Kiểm tra mật khẩu hiện tại
    if (!password_verify($currentPassword, $hashedCurrentPassword)) {
        $error = "Mật khẩu hiện tại không đúng.";
    } else {
        // Kiểm tra mật khẩu mới
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/', $newPassword)) {
            $error = "Mật khẩu mới phải có ít nhất 8 ký tự, bao gồm chữ số và chữ in hoa.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "Xác nhận mật khẩu không khớp.";
        } else {
            // Mã hóa mật khẩu mới và cập nhật vào cơ sở dữ liệu
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql_update = "UPDATE Users SET password = ? WHERE userId = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ss", $hashedNewPassword, $userId);
            $stmt_update->execute();

            // Chuyển hướng về trang thông tin người dùng
            header("Location: PProfileUser.php?userId=" . urlencode($userId));
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thay đổi mật khẩu</title>
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
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="currentPassword">Mật khẩu hiện tại</label>
                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
            </div>
            <div class="form-group">
                <label for="newPassword">Mật khẩu mới</label>
                <input type="password" class="form-control" id="newPassword" name="newPassword" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Xác nhận mật khẩu</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="showPasswords" onclick="togglePasswordVisibility()">
                <label class="form-check-label" for="showPasswords">Hiển thị mật khẩu</label>
            </div>
            <center>
                <button type="button" class="btn btn-secondary" onclick="confirmBack()">Quay lại</button>
                <button type="submit" class="btn btn-primary" onclick="return confirm('Bạn có chắc chắn với lựa chọn này?')">Lưu</button>
            </center>
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
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const passwordPattern = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;

            if (!passwordPattern.test(newPassword)) {
                alert('Mật khẩu mới phải có ít nhất 8 ký tự, bao gồm chữ số và chữ in hoa.');
                return false;
            }

            if (newPassword !== confirmPassword) {
                alert('Xác nhận mật khẩu không khớp.');
                return false;
            }

            return true;
        }

        function togglePasswordVisibility() {
            const currentPassword = document.getElementById('currentPassword');
            const newPassword = document.getElementById('newPassword');
            const confirmPassword = document.getElementById('confirmPassword');
            const type = currentPassword.type === 'password' ? 'text' : 'password';
            currentPassword.type = type;
            newPassword.type = type;
            confirmPassword.type = type;
        }
    </script>
</body>
</html>
<?php
include('../template/Tfooter.php');
$conn->close();
?>