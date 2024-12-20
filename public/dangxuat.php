<?php
session_start(); // Bắt đầu session

// Hủy bỏ tất cả các session để đăng xuất người dùng
session_unset();  // Xóa tất cả các biến session
session_destroy(); // Hủy session

// Điều hướng người dùng quay về trang chủ sau khi đăng xuất
header('Location: trangchu.php');
exit();
?>
