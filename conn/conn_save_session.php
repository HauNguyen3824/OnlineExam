<?php
session_start(); // Bắt đầu session

// Kiểm tra nếu người dùng đã đăng nhập, nếu không chuyển hướng đến trang đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: PLogin.php"); // Chuyển đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

// Lấy thông tin người dùng từ session
$username = $_SESSION['username'];
?>