<?php
// save_exam_users.php
include('../conn/conn_database.php');

function generateAUId($conn) {
    $prefix = "AU";
    do {
        $random_number = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // Random number
        $auId = $prefix . $random_number;
        $sql = "SELECT AUId FROM AddUser WHERE AUId = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $auId);
        $stmt->execute();
        $stmt->store_result(); // fetch results
    } while ($stmt->num_rows > 0); // repeat random number gen till result return 0

    return $auId;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $examId = $_POST['examId'] ?? null;
    $userIds = $_POST['userIds'] ?? [];

    if (!$examId || empty($userIds)) {
        echo "error:Invalid data.";
        exit;
    }

    foreach ($userIds as $userId) {
        $auId = generateAUId($conn); // generate unique id for insertion, use same database to access
        $sql = "INSERT INTO AddUser (AUId, ExamId, UserId) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $auId, $examId, $userId);

        if (!$stmt->execute()) {
            if ($conn->errno === 1062) { // Check for duplicate key error
                echo "error:Người dùng đã tồn tại trong đề thi.";
            } else {
                echo "error:Lỗi khi thêm người dùng: " . $stmt->error; // Other errors
            }
            exit;
        }

        $stmt->close();
    }

    echo "success";
}
?>
