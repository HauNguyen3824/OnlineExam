<?php
// filepath: /d:/xampp/htdocs/doan/php/PRecovery.php
session_start();
include('../php/conn/conn_database.php');

// Hàm tạo RecoveryCode ngẫu nhiên
function generateRecoveryCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $recoveryCode = '';
    for ($i = 0; $length > $i; $i++) {
        $recoveryCode .= $characters[rand(0, $charactersLength - 1)];
    }
    return $recoveryCode;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $recoveryCode = $_POST['recoveryCode'] ?? '';

    if ($username && $recoveryCode) {
        $sql = "SELECT * FROM Users WHERE Username = ? AND RecoveryCode = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $recoveryCode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Tạo RecoveryCode mới
            $newRecoveryCode = generateRecoveryCode();
            $sql_update = "UPDATE Users SET RecoveryCode = ? WHERE Username = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ss", $newRecoveryCode, $username);
            $stmt_update->execute();

            // Chuyển hướng đến trang đặt lại mật khẩu
            header('Location: PChangePass.php?userId=' . urlencode($username));
            exit;
        } else {
            $error_message = "Sai tên đăng nhập hoặc mã khôi phục.";
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
    <title>Khôi phục mật khẩu</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .recovery-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .recovery-container h2 {
            margin-bottom: 20px;
        }
        .recovery-container .form-group {
            margin-bottom: 15px;
        }
        .recovery-container .btn {
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
                <div class="recovery-container">
                    <h2 class="text-center">Khôi phục mật khẩu</h2>
                    <?php if ($error_message): ?>
                        <div class="error-message text-center"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>
                    <form method="post" action="PRecovery.php">
                        <div class="form-group">
                            <label for="username">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="recoveryCode">Mã khôi phục</label>
                            <input type="text" class="form-control" id="recoveryCode" name="recoveryCode" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Xác nhận</button>
                        <a href="index.php" class="btn btn-secondary mt-2">Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
        include '../php/template/Tfooter.php';
    ?>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>