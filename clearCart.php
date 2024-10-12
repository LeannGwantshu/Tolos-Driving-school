<?php
session_start();
include 'data.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['student_id'])) {
    $studentId = $data['student_id'];

    // Delete all cart items for this student
    $stmt = $conn->prepare("DELETE FROM cart WHERE student_id = ?");
    $stmt->bind_param("i", $studentId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to clear cart']);
    }

    $stmt->close();
}

$conn->close();
?>
