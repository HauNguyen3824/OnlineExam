<?php
// filepath: /d:/xampp/htdocs/doan/php/PageUser/PChat.php
session_start();
include('../conn/conn_database.php');

// Lấy userId từ session
$userId = $_SESSION['userId'] ?? null;

if (!$userId) {
    die("Thiếu thông tin người dùng.");
}

// Xử lý khi người dùng gửi tin nhắn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';

    if (!empty($content)) {
        // Lấy chatId mới nhất từ bảng chat
        $sql_get_chatid = "SELECT MAX(chatid) AS maxChatId FROM chat";
        $result_get_chatid = $conn->query($sql_get_chatid);
        $row = $result_get_chatid->fetch_assoc();
        $new_chatid = $row['maxChatId'] ? $row['maxChatId'] + 1 : 1;

        // Thêm tin nhắn vào bảng chat
        $sql_insert = "INSERT INTO chat (chatid, content, seen, userid) VALUES (?, ?, 0, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iss", $new_chatid, $content, $userId);
        $stmt_insert->execute();

        if ($stmt_insert->affected_rows > 0) {
            echo "<script>alert('Gửi tin nhắn thành công.'); window.location.href = 'PMenuUser.php';</script>";
        } else {
            echo "<script>alert('Gửi tin nhắn thất bại.');</script>";
        }
    } else {
        echo "<script>alert('Nội dung tin nhắn không được để trống.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi tin nhắn đến Admin</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmSubmit() {
            return confirm('Bạn có chắc chắn muốn gửi tin nhắn này không?');
        }

        function confirmBack() {
            if (confirm('Quay lại sẽ không lưu dữ liệu hiện tại. Bạn có chắc chắn muốn quay lại?')) {
                window.location.href = 'PMenuUser.php';
            }
        }
    </script>
</head>
<body>
    <?php include '../template/TmenuUser.php'; ?>
    <header class="bg-primary text-white text-center py-3">
        <h1>Gửi báo cáo đến Admin</h1>
    </header>
    <div class="container mt-4">
        <form method="post" onsubmit="return confirmSubmit()">
            <div class="form-group">
                <b><label for="content">Nội dung tin nhắn:</label></b>
                <textarea class="form-control" id="content" name="content" rows="5" placeholder = "Nhập nội dung báo cáo ở đây..." required></textarea>
            </div>
            <center><button type="submit" class="btn btn-primary">Gửi</button>
            <button type="button" class="btn btn-secondary" onclick="confirmBack()">Quay lại</button></center>
        </form>
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