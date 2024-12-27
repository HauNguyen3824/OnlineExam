<?php
// filepath: /d:/xampp/htdocs/doan/php/template/TmenuUser.php
$userId = $_SESSION['userId'] ?? null;
?>
<div id="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#" id="logo">
            <img src="../img/logohcmue.png" alt="logo" width="100">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="../PageUser/PMenuUser.php?userId=<?= htmlspecialchars($userId) ?>">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="../PageUser/PExamView.php?userId=<?= htmlspecialchars($userId) ?>">Đề thi</a></li>
                <li class="nav-item"><a class="nav-link" href="../PageUser/PHistory.php?userId=<?= htmlspecialchars($userId) ?>">Lịch sử làm bài</a></li>
                <li class="nav-item"><a class="nav-link" href="../PageUser/PProfileUser.php?userId=<?= htmlspecialchars($userId) ?>">Thông tin cá nhân</a></li>
                <li class="nav-item"><a class="nav-link" href="../PageUser/PChat.php?userId=<?= htmlspecialchars($userId) ?>">Báo cáo</a></li>
                <li class="nav-item"><a class="nav-link" href="../conn/SignOut.php">Đăng xuất</a></li>
            </ul>
        </div>
    </nav>
</div>