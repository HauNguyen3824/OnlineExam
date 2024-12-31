<?php
// filepath: /d:/xampp/htdocs/doan/php/PLogin.php
session_start();
// Thông tin kết nối cơ sở dữ liệu
$servername = "localhost";  // Địa chỉ máy chủ MySQL (thường là localhost)
$username = "root";         // Tên người dùng MySQL
$password = "";             // Mật khẩu MySQL (nếu có)
$dbname = "onlineexam";     // Tên cơ sở dữ liệu bạn muốn kết nối

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $sql = "SELECT * FROM Users WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Kiểm tra mật khẩu
            if (password_verify($password, $user['Password'])) {
                $_SESSION['userId'] = $user['UserId'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['role'] = $user['Role'];

                // Kiểm tra vai trò của người dùng và độ dài mật khẩu
                if ($user['Role'] === 'user' && strlen($password) < 5) {
                    header('Location: ../php/PageUser/PFirstSetPass.php?userId=' . $user['UserId']);
                } elseif ($user['Role'] === 'admin') {
                    header('Location: ../php/PageAdmin/PMenuAdmin.php?userId=' . $user['UserId']);
                } else {
                    header('Location: ../php/PageUser/PMenuUser.php?userId=' . $user['UserId']);
                }
                exit;
            } else {
                $error_message = "Tên đăng nhập hoặc mật khẩu không đúng.";
            }
        } else {
            $error_message = "Tên đăng nhập hoặc mật khẩu không đúng.";
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
    <title>Đăng nhập</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container .form-group {
            margin-bottom: 15px;
        }
        .login-container .btn {
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
                <div class="login-container">
                    <h2 class="text-center">Đăng nhập</h2>
                    <?php if ($error_message): ?>
                        <div class="error-message text-center"><?= htmlspecialchars($error_message) ?></div>
                    <?php endif; ?>
                    <form method="post" action="PLogin.php">
                        <div class="form-group">
                            <label for="username">Tên đăng nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="showPassword" onclick="togglePasswordVisibility()">
                            <label class="form-check-label" for="showPassword">Hiển thị mật khẩu</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Đăng nhập</button>
                        <div class="form-group">
                            <p>Quên mật khẩu? <a href="PRecovery.php" class="text-primary">Nhập mã khôi phục</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
        include '../php/template/Tfooter.php';
    ?>
    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>