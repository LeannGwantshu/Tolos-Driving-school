<?php
session_start();
include 'data.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_Id = $_POST['user_id'];
    $courseIds = explode(',', $_POST['course_ids']); // Get course IDs as an array
    $Method = 'cash';
    $amount = $_POST['amount'];
    $paymentDate = date('Y-m-d H:i:s');

    // Insert into payments table
    $stmt = $conn->prepare("INSERT INTO payments (student_id, amount, payment_date, method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $student_Id, $amount, $paymentDate, $Method);

    if ($stmt->execute()) {
        $paymentId = $stmt->insert_id; // Get the last inserted payment ID

        // Insert into course_registration table for each course
        foreach ($courseIds as $courseId) {
            $stmt = $conn->prepare("INSERT INTO course_registrations (student_id, course_id, registration_date) VALUES (?, ?, ?)");
            $registrationDate = date('Y-m-d H:i:s');
            $stmt->bind_param("iis", $student_Id, $courseId, $registrationDate);
            $stmt->execute();
        }

        // Success, redirect to the dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
        input[type="number"] {
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
<h2>Checkout</h2>
<form method="POST">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
    <input type="hidden" name="course_ids" value="<?php echo htmlspecialchars($_GET['course_ids']); ?>">
    <input type="number" name="amount" placeholder="Total Amount" required>
    <button type="submit">Finish Order</button>
</form>
</body>
</html>
