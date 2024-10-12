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
$studentId = $data['studentId'];
$instructorId = $data['instructorId']; // Set the appropriate instructor ID
$lessonDate = $data['lessonDate'];
$courseId = $data['courseId'];
$duration = $data['duration'];

// Prepare the SQL statement to insert the scheduled lesson
$stmt = $conn->prepare("INSERT INTO course_schedule (course_id, student_id, instructor_id, lesson_date, duration, status) VALUES (?, ?, ?, ?, ?, 'scheduled')");
$stmt->bind_param("iiisi", $courseId, $studentId, $instructorId, $lessonDate, $duration); // Adjust types if needed

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
