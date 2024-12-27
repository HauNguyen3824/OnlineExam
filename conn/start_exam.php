<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $examId = $_POST['examId'] ?? null;

    if ($examId) {
        $_SESSION['examId'] = $examId;
        echo 'success';
    } else {
        echo 'error: Exam ID is missing';
    }
} else {
    echo 'error: Invalid request method';
}
?>