<?php
// filepath: /d:/xampp/htdocs/doan/php/PageAdmin/PChatManagement.php
session_start();
include('../conn/conn_database.php');

// Truy vấn bảng chat để lấy dữ liệu
$sql_chat = "SELECT chatid, userid, content, seen FROM chat";
$result_chat = $conn->query($sql_chat);

if ($result_chat === false) {
    die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
}

$chats = [];
while ($row = $result_chat->fetch_assoc()) {
    $chats[] = $row;
}

// Truy vấn bảng Users để lấy thông tin người dùng
$user_info = [];
if (!empty($chats)) {
    $user_ids = array_unique(array_column($chats, 'userid'));
    $placeholders = implode(',', array_fill(0, count($user_ids), '?'));
    $sql_users = "SELECT userId, fullName FROM Users WHERE userId IN ($placeholders)";
    $stmt_users = $conn->prepare($sql_users);
    $stmt_users->bind_param(str_repeat('s', count($user_ids)), ...$user_ids);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();

    if ($result_users === false) {
        die("Lỗi truy vấn cơ sở dữ liệu: " . $conn->error);
    }

    while ($row = $result_users->fetch_assoc()) {
        $user_info[$row['userId']] = $row;
    }
}

// Xử lý yêu cầu xóa tin nhắn
if (isset($_POST['delete'])) {
    $chatid = $_POST['chatid'];

    // Xóa tin nhắn từ bảng chat
    $sql_delete = "DELETE FROM chat WHERE chatid = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $chatid);
    $stmt_delete->execute();

    if ($stmt_delete->affected_rows > 0) {
        echo "<script>alert('Xóa tin nhắn thành công.'); window.location.href = 'PChatManagement.php';</script>";
    } else {
        echo "<script>alert('Xóa tin nhắn thất bại.');</script>";
    }
}

// Xử lý yêu cầu đánh dấu đã đọc tin nhắn
if (isset($_POST['mark_as_read'])) {
    $chatid = $_POST['chatid'];

    // Cập nhật trạng thái đã đọc
    $sql_update = "UPDATE chat SET seen = 1 WHERE chatid = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $chatid);
    $stmt_update->execute();

    if ($stmt_update->affected_rows > 0) {
        echo "<script>window.location.href = 'PChatManagement.php';</script>";
    } else {
        echo "<script>alert('Cập nhật trạng thái thất bại.');</script>";
    }
}

// Xử lý yêu cầu đánh dấu chưa đọc tin nhắn
if (isset($_POST['mark_as_unread'])) {
    $chatid = $_POST['chatid'];

    // Cập nhật trạng thái chưa đọc
    $sql_update = "UPDATE chat SET seen = 0 WHERE chatid = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $chatid);
    $stmt_update->execute();

    if ($stmt_update->affected_rows > 0) {
        echo "<script>window.location.href = 'PChatManagement.php';</script>";
    } else {
        echo "<script>alert('Cập nhật trạng thái thất bại.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tất cả tin nhắn</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        tr {
            text-align: center;
        }
    </style>
    <script>
        function confirmDelete(form) {
            if (confirm('Bạn có chắc chắn muốn xóa tin nhắn này không?')) {
                form.submit();
            }
        }
    </script>
</head>
<body>
    <?php include '../template/Tmenubar.php'; ?>
    <header class="bg-primary text-white text-center py-3">
        <h1>Báo Cáo</h1>
    </header>
    <div class="container mt-4">
        <h2 class="text-center">Tất cả tin nhắn</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Số thứ tự</th>
                    <th>UserId</th>
                    <th>Họ và tên</th>
                    <th>Nội dung</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($chats)): ?>
                    <?php foreach ($chats as $index => $chat): ?>
                        <?php
                        $user = $user_info[$chat['userid']] ?? null;
                        $btn_class = $chat['seen'] == 0 ? 'btn-warning' : 'btn-success';
                        ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($chat['userid']) ?></td>
                            <td><?= htmlspecialchars($user['fullName'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($chat['content']) ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="chatid" value="<?= htmlspecialchars($chat['chatid']) ?>">
                                    <button type="submit" name="mark_as_read" class="btn <?= $btn_class ?>" <?= $chat['seen'] == 1 ? 'disabled' : '' ?>>Đánh dấu là đã đọc</button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="chatid" value="<?= htmlspecialchars($chat['chatid']) ?>">
                                    <button type="submit" name="mark_as_unread" class="btn btn-secondary" <?= $chat['seen'] == 0 ? 'disabled' : '' ?>>Đánh dấu là chưa đọc</button>
                                </form>
                                <form method="post" style="display:inline;" onsubmit="return confirmDelete(this);">
                                    <input type="hidden" name="chatid" value="<?= htmlspecialchars($chat['chatid']) ?>">
                                    <button type="submit" name="delete" class="btn btn-danger">Xóa tin nhắn</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Không có dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
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