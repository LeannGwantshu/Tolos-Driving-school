<?php
session_start();
include 'data.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to proceed.";
    exit();
}

// Fetch cart information from the database
$studentId = $_SESSION['user_id'];
$query = "SELECT ct.course_id, ct.quantity, ct.total_amount, c.name
          FROM cart ct
          JOIN courses c ON ct.course_id = c.id
          WHERE ct.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$cartResult = $stmt->get_result();

// Check if cart is empty
if ($cartResult->num_rows == 0) {
    echo "Your cart is empty. Please add courses before checking out.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $startDate = $_POST['start_date'];
    $paymentMethod = $_POST['payment_method'];

    // Prepare to insert into course_registration and payments
    $registrationDate = date('Y-m-d H:i:s');
    $status = 'registered';

    // Insert into course_registration for each course in the cart
    while ($item = $cartResult->fetch_assoc()) {
        $courseId = $item['course_id'];

        // Insert into course_registration
        $stmt = $conn->prepare("INSERT INTO course_registrations (student_id, course_id, registration_date, status, start_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $studentId, $courseId, $registrationDate, $status, $startDate);
        $stmt->execute();
        $stmt->close();
    }

    // Insert payment
    $paymentDate = date('Y-m-d H:i:s');

    // Reset cart result pointer to calculate total amount for payment
    $cartResult->data_seek(0);
    $totalAmount = 0;

    while ($item = $cartResult->fetch_assoc()) {
        $totalAmount += $item['total_amount']; // Sum total_amount from the cart
    }

    // Insert into payments table
    $stmt = $conn->prepare("INSERT INTO payments (student_id, amount, payment_date, method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $studentId, $totalAmount, $paymentDate, $paymentMethod);
    $stmt->execute();
    $stmt->close();

    // Clear the cart (remove items from the cart table)
    $conn->query("DELETE FROM cart WHERE student_id = $studentId");

    // Redirect to dashboard
    header("Location: dashboard.php");
    exit();
}

// Reset the cart result pointer for display
$cartResult->data_seek(0);
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
        input[type="date"],
        select {
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
        <div class="cart-items">
        <?php while ($cartItem = $cartResult->fetch_assoc()): ?>
            <p>Course Name: <?php echo htmlspecialchars($cartItem['name']); ?> - Price: R<?php echo number_format($cartItem['total_amount'], 2); ?> - Quantity: <?php echo htmlspecialchars($cartItem['quantity']); ?></p>
        <?php endwhile; ?>
    </div>
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" required>

        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" required>
            <option value="credit_card">Credit Card</option>
            <option value="debit_card">Debit Card</option>
            <option value="cash">CASH</option>
            <option value="bank_transfer">EFT</option>
        </select>

        <div class="total-amount">
            <?php
            // Reset the cart result pointer to calculate total amount
            $cartResult->data_seek(0);
            $totalAmount = 0;

            while ($item = $cartResult->fetch_assoc()) {
                $totalAmount += $item['total_amount']; // Sum total_amount from the cart
            }
            ?>
            <h3>Total Amount: R<?php echo number_format($totalAmount, 2); ?></h3>
        </div>

        <button type="submit">Complete Checkout</button>
    </form>
</body>
</html>
