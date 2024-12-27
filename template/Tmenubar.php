<?php
// filepath: /d:/xampp/htdocs/doan/php/template/Tmenubar.php
include('../conn/conn_database.php');

// Kiểm tra nếu có tin nhắn chưa đọc
$sql_check_seen = "SELECT COUNT(*) AS unread_count FROM chat WHERE seen = 0";
$result_check_seen = $conn->query($sql_check_seen);
$row_check_seen = $result_check_seen->fetch_assoc();
$unread_count = $row_check_seen['unread_count'] ?? 0;
$report_class = $unread_count > 0 ? 'text-danger' : '';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#" id="logo">
        <img src="../img/logohcmue.png" alt="logo" width="100">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="../PageAdmin/PMenuAdmin.php">Trang chủ</a></li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Quản lý
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../PageAdmin/PUserManagement.php">Người dùng</a>
                    <a class="dropdown-item" href="../PageAdmin/PExamManagement.php">Đề thi</a>
                    <a class="dropdown-item" href="../PageAdmin/PQuesManagement.php">Câu hỏi</a>
                    <a class="dropdown-item" href="../PageAdmin/PResultManagement.php">Kết quả</a>
                </div>
            </li>
            <li class="nav-item"><a class="nav-link" href="../PageAdmin/PRegister.php">Đăng ký</a></li>
            <li class="nav-item"><a class="nav-link <?= $report_class ?>" href="../PageAdmin/PChatManagement.php">Báo cáo</a></li>
            <li class="nav-item"><a class="nav-link" href="../conn/SignOut.php">Đăng xuất</a></li>
        </ul>
    </div>
</nav>

<!-- Bao gồm các tệp JavaScript cần thiết -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>