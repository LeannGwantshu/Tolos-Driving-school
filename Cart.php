<?php
session_start();
include 'data.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['userId'], $data['courseId'], $data['quantity'], $data['totalAmount'])) {
    $userId = $data['userId'];
    $courseId = $data['courseId'];
    $quantity = $data['quantity'];
    $totalAmount = $data['totalAmount'];

    // Check if the course ID exists
    $courseCheckStmt = $conn->prepare("SELECT id FROM courses WHERE id = ?");
    $courseCheckStmt->bind_param("i", $courseId);
    $courseCheckStmt->execute();
    $courseCheckStmt->store_result();

    if ($courseCheckStmt->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Course ID does not exist']);
        exit();
    }
    $courseCheckStmt->close();

    // Insert into the cart table
    $stmt = $conn->prepare("INSERT INTO cart (student_id, course_id, quantity, total_amount, added_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiid", $userId, $courseId, $quantity, $totalAmount);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add item to cart']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}

$conn->close();

?>
