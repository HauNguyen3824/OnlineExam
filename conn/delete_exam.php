<?php
// delete_exam.php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_exam'])) { // Kết hợp điều kiện
    include('../conn/conn_database.php');

    // Kiểm tra kết nối (nên đặt ở đầu file conn_database.php)
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    $examIdToDelete = filter_input(INPUT_POST, 'ExamIdToDelete', FILTER_SANITIZE_STRING);

    if (!empty($examIdToDelete)) {
        $deleteSql = "DELETE FROM Exams WHERE ExamId = ?";
        $deleteStmt = $conn->prepare($deleteSql);

        if (!$deleteStmt) { // Rút gọn điều kiện
            echo "error:Lỗi prepare: " . $conn->error;
            exit; // Không cần đóng kết nối ở đây, vì nó sẽ được đóng ở cuối file
        }

        $deleteStmt->bind_param("s", $examIdToDelete);

        if ($deleteStmt->execute()) {
            if ($deleteStmt->affected_rows > 0) {
                echo 'success';
            } else {
                echo 'error:Không tìm thấy ExamId';
            }
        } else {
            echo "error:Lỗi execute: " . $deleteStmt->error;
        }

        $deleteStmt->close();
    } else {
        echo 'error:ExamId không được để trống';
    }

    $conn->close();
} else {
    echo 'error:Yêu cầu không hợp lệ';
}
?>