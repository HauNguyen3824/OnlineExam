<?php
    include 'head.php';
?>
<body>
    <?php 
        include 'header.php'; 
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM UserDetails WHERE UserID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $userDetails = $result->fetch_assoc();

        
    ?>
    <style>
        .avatar{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            float: left;
            margin-right: 20px;
            gap: 20px;
            width: 250px;
        }
        .avatar img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
        }
        form{
            width: calc(100% - 290px);
            margin-right: 20px;
        }
        form h4{
            font-weight: bold;
            font-size: 15pt;
            color: #848D92 !important;
            margin-top: 0 !important;
        }
        .container.article{
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .buttons{
            float: right;
        }
    </style>
    <div class="hero overlay" style="height: 150px !important; max-height: 150px !important; min-height: 100px !important">
	</div>
    <div class="section">
        <div class="container article">
            <div class="avatar">
                <img src="images/<?php echo $userDetails['Avatar'] ? $userDetails['Avatar'] : 'no-avatar.jpg'; ?>" alt="avatar">
                <button type="button" class="btn btn-primary open-avatar-selector">Chỉnh sửa ảnh đại diện</button>
            </div>
            <form action="process.php" method="post" enctype="multipart/form-data">
                <h4>CHỈNH SỬA THÔNG TIN CÁ NHÂN</h4>
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="avatar" value="<?php echo $userDetails['Avatar']; ?>">
                <div class="form-group">
                    <input type="file" name="avatarInput" id="avatarInput" accept="image/*" style="display: none;">
                </div>
                <div class="form-group">
                    <label for="fullname">Họ và tên</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo $userDetails['Fullname']; ?>">
                </div>
                <div class="form-group">
                    <label for="dob">Ngày sinh</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $userDetails['DateOfBirth']; ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $userDetails['Email']; ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $userDetails['PhoneNumber']; ?>">
                </div>
                <div class="form-group buttons">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">Quay lại</button>
                    <button type="button" class="btn btn-danger" onclick="window.location.href='DoiMatKhau.php'">Đổi mật khẩu</button>
                    <button name="action" value="updateUserDetails" class="btn btn-success">Lưu thông tin</button>
                </div>

            </form>
        </div>
    </div>
    <script src="js/InformationEdition.js"></script>
    <?php 
        include 'footer.php';
        include 'javascript.php';
    ?>
</body>
</html>