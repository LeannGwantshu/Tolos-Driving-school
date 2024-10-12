<?php
session_start();
include 'data.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userType = $_POST['user_type']; // Determine if it's a student or instructor

    if ($userType === 'student') {
        // Check student credentials
        $stmt = $conn->prepare("SELECT * FROM students WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                header("Location: dashboard.php");
                exit();
            } else {
                echo "<p style='color: red;'>Invalid password.</p>";
            }
        } else {
            echo "<p style='color: red;'>No student found.</p>";
        }

    } elseif ($userType === 'instructor') {
        // Check instructor credentials
        $stmt = $conn->prepare("SELECT * FROM instructors WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['instructor_name'] = $row['name']; // Save instructor name in session
                header("Location: instructorsPage.php"); // Redirect to instructor page
                exit();
            } else {
                echo "<p style='color: red;'>Invalid password.</p>";
            }
        } else {
            echo "<p style='color: red;'>No instructor found.</p>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            background-image: url('https://i.ibb.co/vXvQQtr/bacgrond-App.webp');
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background:transparent;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #CE2D1E;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #000000;
        }
        p {
            text-align: center;
            color: red; /* Error messages will be red */
        }
        img { border-radius: 10px; }
        h2 {color:#ffffff}
    </style>
</head>
<body>
<form method="POST">
<img src="https://i.ibb.co/P15TFTp/cover-Tolo.png" alt="cover-Tolo" border="0">
<h2>Login</h2>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <label for="user_type">Login as:</label>
    <select name="user_type" required>
        <option value="student">Student</option>
        <option value="instructor">Instructor</option>
    </select>
    <button type="submit">Login</button>
</form>
</body>
</html>
