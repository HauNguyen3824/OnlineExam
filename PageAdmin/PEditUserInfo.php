<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PEditUserInfo.php
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

// Xử lý form khi người dùng nhấn nút "Xác nhận thay đổi"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['allowEdit'])) {
        die("Bạn phải tích vào checkbox để cho phép chỉnh sửa.");
    }

    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $class = $_POST['class'] ?? '';
    $year = $_POST['year'] ?? '';

    // Cập nhật thông tin người dùng trong cơ sở dữ liệu
    $sql_update = "UPDATE Users SET fullName = ?, email = ?, phone = ?, class = ?, year = ? WHERE userId = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssss", $fullName, $email, $phone, $class, $year, $userId);
    $stmt_update->execute();

    // Chuyển hướng về trang quản lý người dùng
    header("Location: PUserManagement.php");
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
    <script>
        function toggleEdit() {
            const isEditable = document.getElementById('allowEdit').checked;
            document.getElementById('fullName').readOnly = !isEditable;
            document.getElementById('email').readOnly = !isEditable;
            document.getElementById('phone').readOnly = !isEditable;
            document.getElementById('class').readOnly = !isEditable;
            document.getElementById('year').readOnly = !isEditable;
        }

        function confirmSubmit() {
            if (!document.getElementById('allowEdit').checked) {
                alert('Bạn phải tích vào checkbox để cho phép chỉnh sửa.');
                return false;
            }
            return confirm('Bạn có chắc chắn muốn thay đổi thông tin này?');
        }

        function confirmBack() {
            if (confirm('Quay lại sẽ không lưu dữ liệu hiện tại. Bạn có chắc chắn muốn quay lại?')) {
                window.location.href = 'PUserManagement.php';
            }
        }
    </script>
</head>
<body>
    <div class="container mt-4">
        <header class="bg-primary text-white text-center py-3">
            <h1>Quản Lý Người Dùng</h1>
        </header>
        <div class="card mt-4">
            <div class="card-header">
                <h2>Chỉnh sửa thông tin người dùng</h2>
            </div>
            <div class="card-body">
                <form method="post" onsubmit="return confirmSubmit()">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="userId">UserId</label>
                            <input type="text" class="form-control" id="userId" name="userId" value="<?= htmlspecialchars($user['userId']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fullName">Họ và tên</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" value="<?= htmlspecialchars($user['fullName']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="class">Lớp</label>
                            <input type="text" class="form-control" id="class" name="class" value="<?= htmlspecialchars($user['class']) ?>" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="year">Năm nhập học</label>
                            <input type="text" class="form-control" id="year" name="year" value="<?= htmlspecialchars($user['year']) ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="allowEdit" name="allowEdit" onclick="toggleEdit()">
                        <label class="form-check-label" for="allowEdit">Cho phép chỉnh sửa</label>
                    </div>
                    <center>
                    <button type="submit" class="btn btn-primary">Xác nhận thay đổi</button>
                    <button type="button" class="btn btn-secondary" onclick="confirmBack()">Quay lại</button>
                    </center>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
include('../template/Tfooter.php');
$conn->close();
?>