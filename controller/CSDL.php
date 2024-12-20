<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $db_name = "Quản lý người dùng";
        $connect = new mysqli ($servername, $username, $password, $db_name);
        if ($connect -> connect_error) die ("Connection failed: ". $connect -> connect_error);
?>
</body>
</html>