<?php
session_start();
include 'data.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Get the input data from the AJAX request
$data = json_decode(file_get_contents('php://input'), true);
$courseId = $data['courseId'];

// Prepare the SQL statement to delete the course registration
$stmt = $conn->prepare("DELETE FROM course_registrations WHERE student_id = ? AND course_id = ?");
$stmt->bind_param("ii", $userId, $courseId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
