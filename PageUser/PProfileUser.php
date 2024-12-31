<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PProfileUser.php
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin người dùng</title>
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
        tr {
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../template/TmenuUser.php'; ?>
    <header class="bg-primary text-white text-center py-3">
        <h1>Thông tin người dùng</h1>
    </header>
    <div class="container">
        <table class="table table-bordered mt-4">
            <tr>
                <th>UserId</th>
                <td><?= htmlspecialchars($user['userId']) ?></td>
            </tr>
            <tr>
                <th>Họ và tên</th>
                <td><?= htmlspecialchars($user['fullName']) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($user['email']) ?></td>
            </tr>
            <tr>
                <th>Số điện thoại</th>
                <td><?= htmlspecialchars($user['phone']) ?></td>
            </tr>
            <tr>
                <th>Lớp</th>
                <td><?= htmlspecialchars($user['class']) ?></td>
            </tr>
            <tr>
                <th>Năm nhập học</th>
                <td><?= htmlspecialchars($user['year']) ?></td>
            </tr>
        </table>
        <center>
            <a href="PEditInfo.php?userId=<?= htmlspecialchars($userId) ?>" class="btn btn-primary">Thay đổi thông tin</a>
            <a href="PEditPass.php?userId=<?= htmlspecialchars($userId) ?>" class="btn btn-warning">Thay đổi mật khẩu</a>
            <button type="button" class="btn btn-info" onclick="showRecoveryCode()">Xem mã khôi phục</button>
        </center>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="recoveryCodeModal" tabindex="-1" role="dialog" aria-labelledby="recoveryCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recoveryCodeModalLabel">Mã khôi phục</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="recoveryCodeMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function showRecoveryCode() {
            const userId = <?= json_encode($userId) ?>;
            $.ajax({
                url: '../conn/get_recovery_code.php',
                type: 'GET',
                data: { userId: userId },
                success: function(response) {
                    $('#recoveryCodeMessage').text('Đây là mã khôi phục dùng một lần của UserId ' + userId + ': ' + response);
                    $('#recoveryCodeModal').modal('show');
                }
            });
        }
    </script>
</body>
</html>
<?php
$stmt_user->close();
include('../template/Tfooter.php');
$conn->close();
?>