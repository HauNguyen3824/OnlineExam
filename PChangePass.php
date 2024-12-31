<?php
// filepath: /d:/xampp/htdocs/doan/php/PChangePass.php
session_start();
include('../php/conn/conn_database.php');

// Lấy userId từ URL
$userId = $_GET['userId'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    if ($newPassword && $confirmPassword) {
        if ($newPassword === $confirmPassword) {
            if (strlen($newPassword) >= 8 && preg_match('/[A-Z]/', $newPassword) && preg_match('/[0-9]/', $newPassword)) {
                // Mã hóa mật khẩu
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Cập nhật mật khẩu mới vào cơ sở dữ liệu
                $sql_update = "UPDATE Users SET Password = ? WHERE UserId = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ss", $hashedPassword, $userId);
                $stmt_update->execute();

                if ($stmt_update->affected_rows > 0) {
                    echo "<script>alert('Mật khẩu đã được cập nhật thành công.');</script>";
                    header('Location: index.php');
                    exit;
                } else {
                    $error_message = "Cập nhật mật khẩu thất bại.";
                }
            } else {
                $error_message = "Mật khẩu phải ít nhất 8 ký tự, có chữ số và chữ in hoa.";
            }
        } else {
            $error_message = "Mật khẩu xác nhận không khớp.";
        }
    } else {
        $error_message = "Vui lòng điền đầy đủ thông tin.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .password-reset-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .password-reset-container h2 {
            margin-bottom: 20px;
        }
        .password-reset-container .form-group {
            margin-bottom: 15px;
        }
        .password-reset-container .btn {
            width: 100%;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="password-reset-container">
                    <h2 class="text-center">Đặt lại mật khẩu</h2>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger text-center"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>
                    <form method="post" action="PChangePass.php?userId=<?= htmlspecialchars($userId) ?>">
                        <div class="form-group">
                            <label for="newPassword">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="showPassword" onclick="togglePasswordVisibility()">
                            <label class="form-check-label" for="showPassword">Hiển thị mật khẩu</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Đặt lại mật khẩu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function togglePasswordVisibility() {
            const newPasswordField = document.getElementById('newPassword');
            const confirmPasswordField = document.getElementById('confirmPassword');
            if (newPasswordField.type === 'password') {
                newPasswordField.type = 'text';
                confirmPasswordField.type = 'text';
            } else {
                newPasswordField.type = 'password';
                confirmPasswordField.type = 'password';
            }
        }
    </script>
</body>
</html>
<?php
    include '../php/template/Tfooter.php';
?>
