<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="template/trangdangnhapvky.css">
    <link rel="stylesheet" href="template/menu.css">
</head>
<body>
    <div id="header">
        <nav class="menuContainer">
            <a href="" onclick="ktThongTin()" id="logo">
                <img src="https://upload.wikimedia.org/wikipedia/vi/thumb/9/9e/Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_Minh.svg/1200px-Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_Minh.svg.png" alt="logo">
            </a>
            <ul id="mainmenu">
                <li><a href="" onclick="ktThongTin()">ABCD</a></li>
                <li><a href="" onclick="ktThongTin()">AWMP</a></li>
                <li><a href="" onclick="ktThongTin()">AK47</a></li>
                <li><a href="" onclick="ktThongTin()">Cài đặt</a>
                    <ul class="sub-menu">
                        <li><a href="" onclick="ktThongTin()">Thông tin cá nhân</a></li>
                        <li><a href="" onclick="ktThongTin()">Đề thi đã tạo</a></li>
                        <li><a href="" onclick="ktThongTin()">Các kiểu</a></li>
                        <li><a href="" onclick="ktThongTin()">Đăng xuất</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
    <div class="container">
        <form class="registration-form" method="post" action="">
            <h2>Đăng ký</h2>
            <div class="input-group">
                <input type="text" name="username" placeholder="Tên đăng nhập">
            </div>
            <div class="input-group">
                <input type="text" name="fullname" placeholder="Họ và tên">
            </div>
            <div class="input-group">
                <input type="text" name="email" placeholder="Email">
            </div>
            <div class="input-group">
                <input type="password" name="password" id="mk" placeholder="Mật khẩu">
            </div>
            <div class="input-group">
                <input type="password" name="passwordConfirm" id="mk1" placeholder="Nhập lại mật khẩu">
            </div>
            <div class="input-group">
                <input type="checkbox" onclick="togglePasswordVisibility()"> Hiển thị mật khẩu
            </div>
            <button type="submit" name="dangky">Đăng ký</button>
            <p> Bạn đã có tài khoản rồi?<a href="trangdangnhap.php" class="text-blue-800"> Đăng nhập</a></p>
            <p><a href="trangthongtin">© Nhóm 6</a></p>
        </form>
    </div>
    <script>
        function togglePasswordVisibility() {
            const passwordField1 = document.getElementById("mk");
            const passwordField2 = document.getElementById("mk1");
            if (passwordField1.type === "password" && passwordField2.type === "password") {
                passwordField1.type = "text";
                passwordField2.type = "text";
            } 
            else {
                passwordField1.type = "password";
                passwordField2.type = "password";
            }
        }
        function ktThongTin() {
            alert("Hãy đăng nhập");
        }
    </script>
    <?php
    include 'CSDL.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordConfirm = $_POST['passwordConfirm'];
        if ($password !== $passwordConfirm) die("Mật khẩu không khớp.");
        $sql = "INSERT INTO nguoidung (Username, HovaTen, Email, MatKhau) VALUES (?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("ssss", $username, $fullname, $email, $password);
        if ($stmt->execute()) echo "Đăng ký thành công!";
        else echo "Lỗi: " . $stmt->error;
        $stmt->close();
        $connect->close();
    }
    ?>
</body>
</html>