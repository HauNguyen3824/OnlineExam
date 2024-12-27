<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('../conn/conn_database.php');

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Lấy và kiểm tra QuesId
    $quesIdToDelete = filter_input(INPUT_POST, 'QuesIdToDelete', FILTER_SANITIZE_STRING);

    if (isset($_POST['delete_question'])) {
        if (!empty($quesIdToDelete)) {
            $deleteSql = "DELETE FROM Questions WHERE QuesId = ?";
            $deleteStmt = $conn->prepare($deleteSql);

            if ($deleteStmt === false) {
                echo "Lỗi prepare: " . $conn->error;
                $conn->close();
                exit;
            }

            $deleteStmt->bind_param("s", $quesIdToDelete);

            if ($deleteStmt->execute()) {
                if ($deleteStmt->affected_rows > 0) {
                    echo 'success'; // Thành công
                } else {
                    echo 'error: Không tìm thấy QuesId'; // Không có dòng nào bị ảnh hưởng
                }
            } else {
                echo "Lỗi execute: " . $deleteStmt->error; // Lỗi thực thi
            }

            $deleteStmt->close();
        } else {
            echo 'error: QuesId không được để trống'; // Kiểm tra QuesId rỗng
        }
    } else {
        echo 'error: Yêu cầu không hợp lệ'; // Trường hợp không nhận được tham số `delete_question`
    }

    $conn->close(); // Đóng kết nối cơ sở dữ liệu
}
?>