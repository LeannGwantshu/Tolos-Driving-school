<?php
session_start();
include 'data.php'; // Include your database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0; // Get course_id from GET parameters

if ($courseId > 0) {
    // Prepare statement to fetch instructors for the given course ID
    $stmt = $conn->prepare("SELECT id, name FROM instructors WHERE course_id = ?");
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    $instructors = [];
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row; // Collect instructors
    }
    $stmt->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($instructors);
} else {
    echo json_encode([]); // No course ID provided
}
?>
