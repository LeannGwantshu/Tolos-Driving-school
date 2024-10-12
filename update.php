<?php
session_start();
include 'data.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Update the profile information in the database
    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $userId);

    if ($stmt->execute()) {
        $_SESSION['student_name'] = $name; // Update session variable
        header("Location: dashboard.php"); // Redirect back to dashboard
    } else {
        echo "<p>Error updating profile.</p>";
    }

    $stmt->close();
}
?>
