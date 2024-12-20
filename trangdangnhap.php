<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="template/trangdangnhapvky.css">
    <link rel="stylesheet" href="template/menu.css">
</head>
<body>
    <div id="header">
        <nav class="menuContainer">
            <a href="" id="logo">
                <img src="https://upload.wikimedia.org/wikipedia/vi/thumb/9/9e/Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_Minh.svg/1200px-Logo_Tr%C6%B0%E1%BB%9Dng_%C4%90%E1%BA%A1i_h%E1%BB%8Dc_S%C6%B0_ph%E1%BA%A1m_Th%C3%A0nh_ph%E1%BB%91_H%E1%BB%93_Ch%C3%AD_Minh.svg.png" alt="logo"></a>
            <ul id="mainmenu">
                <li><a href="" onclick="ktThongTin()">ABCD</a></li>
                <li><a href="" onclick="ktThongTin()">AWMP</a></li>
                <li><a href="" onclick="ktThongTin()">AK47 vjp</a></li>
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
        <form class="registration-form">
            <h2>Đăng nhập</h2>
            <div class="input-group">
                <input type="text" name="username" placeholder="Tên đăng nhập">
            </div>
            <div class="input-group">
                <input type="password" name="password" id="mk" placeholder="Mật khẩu">
            </div>
            <div class="input-group">
                <input type="checkbox" onclick="togglePasswordVisibility()"> Hiển thị mật khẩu
            </div>
            <button type="submit" name="dangnhap">Đăng nhập</button>
            <p> Bạn đã có tài khoản chưa?<a href="trangdangky.php" class="text-blue-800"> Đăng ký</a>
            </p>
            <p>
                <a href="trangthongtin" >© Nhóm 6</a>
            </p>
        </form>
    </div>

    <script>
        //nút ẩn, hiện mk
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("mk");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }

        //làm đại :v
        function ktThongTin() {
            alert("Hãy đăng nhập");
        }
    </script>
</body>
</html>
