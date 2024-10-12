<?php
session_start();
include 'data.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch instructor information
$stmt = $conn->prepare("SELECT name, email FROM instructors WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$instructorProfile = $stmt->get_result()->fetch_assoc();

// Check if instructor information was found
if (!$instructorProfile) {
    echo "Instructor not found.";
    exit();
}
$stmt->close();

// Fetch scheduled lessons for this instructor
$stmt = $conn->prepare("SELECT s.id, s.lesson_date, s.duration, s.status,
                        c.name AS course_name, st.name AS student_name
                        FROM course_schedule s
                        INNER JOIN courses c ON s.course_id = c.id
                        INNER JOIN students st ON s.student_id = st.id
                        WHERE s.instructor_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$scheduleResult = $stmt->get_result();

$scheduledLessons = [];
while ($lesson = $scheduleResult->fetch_assoc()) {
    $scheduledLessons[] = $lesson;
}
$stmt->close();

// Fetch available cars for booking
$stmt = $conn->prepare("SELECT id, make, model, V_year, license_plate, course_code FROM Vehicles WHERE is_available = 1");
$stmt->execute();
$carsResult = $stmt->get_result();

$availableCars = [];
while ($car = $carsResult->fetch_assoc()) {
    $availableCars[] = $car;
}
$stmt->close();

// Fetch booked vehicles for this instructor
$stmt = $conn->prepare("SELECT b.id, b.vehicle_id, v.make, v.model
                        FROM bookings b
                        INNER JOIN Vehicles v ON b.vehicle_id = v.id
                        WHERE b.instructor_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookedCarsResult = $stmt->get_result();

$bookedCars = [];
while ($bookedCar = $bookedCarsResult->fetch_assoc()) {
    $bookedCars[] = $bookedCar;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - Tolos Driving School</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #CE2D1E; color: white; padding: 15px; }
        .header h1 { flex-grow: 1; text-align: center; }
        .content { padding: 15px; }
        .course, .car, .booked-car { border: 1px solid #ccc; margin: 5px; padding: 10px; display: inline-block; }
        .button { background-color: #CE2D1E; color: white; border: none; border-radius: 5px; padding: 10px; cursor: pointer; }
    </style>
</head>
<body>
<div class="header">
    <h1>Welcome, <?php echo htmlspecialchars($instructorProfile['name']); ?>!</h1>
    <button class="button" onclick="logOff()">Log Off</button>
</div>
<div class="content">
    <h2>Your Scheduled Lessons</h2>
    <div class="scheduled-lessons">
        <?php
        if (count($scheduledLessons) > 0) {
            foreach ($scheduledLessons as $lesson) {
                echo '<div class="course">';
                echo '<h3>' . htmlspecialchars($lesson['course_name']) . '</h3>';
                echo '<p>Student: ' . htmlspecialchars($lesson['student_name']) . '</p>';
                echo '<p>Lesson Date: ' . date('Y-m-d H:i', strtotime($lesson['lesson_date'])) . '</p>';
                echo '<p>Duration: ' . htmlspecialchars($lesson['duration']) . ' mins</p>';
                echo '<p>Status: ' . htmlspecialchars($lesson['status']) . '</p>';
                echo '</div>';
            }
        } else {
            echo "<p>No scheduled lessons yet.</p>";
        }
        ?>
    </div>

    <h2>Available Cars for Booking</h2>
    <div class="available-cars">
        <?php
        if (count($availableCars) > 0) {
            foreach ($availableCars as $car) {
                echo '<div class="car">';
                echo '<h3>' . htmlspecialchars($car['make']) . '</h3>';
                echo '<p>' . htmlspecialchars($car['model']) . '</p>';
                echo '<p>' . htmlspecialchars($car['V_year']) . '</p>';
                echo '<p>' . htmlspecialchars($car['license_plate']) . '</p>';
                echo '<p>' . htmlspecialchars($car['course_code']) . '</p>';
                echo '<button class="button" onclick="bookCar(' . $car['id'] . ')">Book Car</button>';
                echo '</div>';
            }
        } else {
            echo "<p>No available cars for booking.</p>";
        }
        ?>
    </div>

    <h2>Your Booked Cars</h2>
    <div class="booked-cars">
        <?php
        if (count($bookedCars) > 0) {
            foreach ($bookedCars as $bookedCar) {
                echo '<div class="booked-car">';
                echo '<h3>' . htmlspecialchars($bookedCar['make']) . ' ' . htmlspecialchars($bookedCar['model']) . '</h3>';
                echo '<button class="button" onclick="deleteBooking(' . $bookedCar['id'] . ', ' . $bookedCar['vehicle_id'] . ')">UnBook</button>';
                echo '</div>';
            }
        } else {
            echo "<p>No booked cars yet.</p>";
        }
        ?>
    </div>
</div>
<script>
    function logOff() {
        window.location.href = 'Welcomepage.php'; // Redirect to logout page
    }

    function bookCar(carId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "book_car.php", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.send(JSON.stringify({ instructorId: <?php echo json_encode($userId); ?>, carId: carId }));

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert('Car booked successfully!');
                location.reload(); // Refresh the page to see updated data
            }
        };
    }

    function deleteBooking(bookingId, vehicleId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_booking.php", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.send(JSON.stringify({ bookingId: bookingId, vehicleId: vehicleId }));

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert('Booking canceled successfully!');
                location.reload(); // Refresh the page to see updated data
            }
        };
    }
</script>
</body>
</html>
