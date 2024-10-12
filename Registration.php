<?php
include 'data.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $date_of_birth = $_POST['date_of_birth'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $enrolled_date = date('Y-m-d H:i:s'); // Set the enrolled date to the current date and time
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;

    if ($age < 16) {
        echo "You must be at least 16 years old to register.";
    } else {
        // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO students (name, surname, phone, email, date_of_birth, enrolled_date, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $surname, $phone, $email, $date_of_birth, $enrolled_date, $username, $password);

    // Execute and check for errors
    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        input[type="text"],
        input[type="email"],
        input[type="date"],
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
            background-color: #5cb85c;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
<h2>Register</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Name" required>
    <input type="text" name="surname" placeholder="Surname" required>
    <input type="text" name="phone" placeholder="Phone" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="date" name="date_of_birth" placeholder="DOB" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>
</body>
</html>
