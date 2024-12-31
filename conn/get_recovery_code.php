<?php
// filepath: /d:/xampp/htdocs/doan/php/conn/get_recovery_code.php
include('../conn/conn_database.php');

// Hàm tạo RecoveryCode ngẫu nhiên
function generateRecoveryCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $recoveryCode = '';
    for ($i = 0; $i < $length; $i++) {
        $recoveryCode .= $characters[rand(0, $charactersLength - 1)];
    }
    return $recoveryCode;
}

$userId = $_GET['userId'] ?? null;

if ($userId) {
    $sql = "SELECT RecoveryCode FROM Users WHERE UserId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['RecoveryCode']) {
        echo htmlspecialchars($row['RecoveryCode']);
    } else {
        // Tạo RecoveryCode mới
        $newRecoveryCode = generateRecoveryCode();
        $sql_update = "UPDATE Users SET RecoveryCode = ? WHERE UserId = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ss", $newRecoveryCode, $userId);
        $stmt_update->execute();
        echo htmlspecialchars($newRecoveryCode);
    }
} else {
    echo "Không tìm thấy mã khôi phục.";
}
?>