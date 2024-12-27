<?php
// filepath: /d:/xampp/htdocs/doan/php/conn/delete_exam_question.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('../conn/conn_database.php');

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    $examId = $_POST['examId'];

    // Kiểm tra xem examId có tồn tại trong bảng AddQues không
    $sql_check = "SELECT * FROM AddQues WHERE ExamId = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $examId);

    if (!$stmt_check->execute()) {
        die("Lỗi thực thi truy vấn kiểm tra: " . $stmt_check->error);
    }

    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Xóa các câu hỏi của đề thi
        $sql_delete = "DELETE FROM AddQues WHERE ExamId = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("s", $examId);

        if ($stmt_delete->execute()) {
            echo "deleted";
        } else {
            die("Lỗi thực thi truy vấn xóa: " . $stmt_delete->error);
        }

        $stmt_delete->close();
    } else {
        echo "error:Không tìm thấy đề thi với ExamId này.";
    }

    $stmt_check->close();
    $conn->close();
} else {
    echo "error:Phương thức yêu cầu không hợp lệ.";
}
?>