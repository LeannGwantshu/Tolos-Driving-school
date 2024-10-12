<?php
session_start();
include 'data.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$studentName = isset($_SESSION['student_name']) ? $_SESSION['student_name'] : 'Student';

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM students WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$studentProfile = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM cart WHERE student_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$cartResult = $stmt->get_result();
$cartItems = [];
while ($cartItem = $cartResult->fetch_assoc()) {
    $cartItems[] = $cartItem;
}
$stmt = $conn->prepare("SELECT c.id, c.name, c.description, c.price, c.imageurl
                        FROM course_registrations cr
                        INNER JOIN courses c ON cr.course_id = c.id
                        WHERE cr.student_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$courseResult = $stmt->get_result();
$registeredCourses = [];
while ($row = $courseResult->fetch_assoc()) {
    $registeredCourses[] = $row;
}
$stmt->close();
$stmt = $conn->prepare("SELECT s.id, s.lesson_date, s.duration, s.status,
                        c.name AS course_name, i.name AS instructor_name
                        FROM course_schedule s
                        INNER JOIN courses c ON s.course_id = c.id
                        INNER JOIN instructors i ON s.instructor_id = i.id
                        WHERE s.student_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$scheduleResult = $stmt->get_result();
$scheduledLessons = [];
while ($lesson = $scheduleResult->fetch_assoc()) {
    $scheduledLessons[] = $lesson;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tolos Driving School Dashboard</title>
    <style>
         body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #CE2D1E; color: white; padding: 15px; }
        .header h1 { flex-grow: 1; text-align: center; }
        .sidebar { width: 250px; float: left; background: #f4f4f4; padding: 15px; }
        .content { margin-left: 100px; padding: 15px; }
        .course { border: 1px transparent; margin: 5px 0; padding: 5px; }
        .More, .LogOff { font-size: 20px; background-color: #CE2D1E; border: none; color: #000000; padding: 10px; cursor: pointer; }
        .courses img { width: 150px; height: auto; }
        .coursesMain, .coursesShort { border:1px solid #CE2D1E; border-radius: 10px ; display: flex; flex-wrap: wrap; justify-content: space-around }
        .hidden { display: none; }
        .login-icon { width: 30px; height: 30px; background-color: #333; border-radius: 50%; position: relative; display: flex; justify-content: center; align-items: center; color: white; font-size: 12px; }
        .login-icon::before { content: ''; width: 15px; height: 15px; border-radius: 50%; background-color: white; position: absolute; top: 3px; }
        .login-icon::after { content: ''; width: 25px; height: 10px; border-radius: 0 0 25px 25px; background-color: white; position: absolute; bottom: 0; }
        .add-to-cart, .clear-Cart { background-color: #007bff; color: white; border: none; border-radius: 5px; padding: 5px 10px; cursor: pointer; }
        .cart-button { background-color: #CE2D1E; color: white; border: none; border-radius: 5px; padding: 10px 15px; cursor: pointer; display: flex; align-items: center; position: relative; margin-bottom: 20px; float: right; }
        .cart-icon { width: 20px; height: 20px; background-color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin-right: 8px; }
        .cart-icon::before { content: '🛒'; font-size: 18px; }
        .course-list { display: none; position: absolute; background-color: white; border: 1px solid #ccc; border-radius: 5px; padding: 10px; margin-top: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 200px; right: 0; }
        .course-list.active { display: block; }
        .total-price { font-weight: bold; margin-top: 10px; }
        .close {border: transparent; color:white; border-radius: 30%; }
        .button {border-radius: 30px; background-color: #CE2D1E; color:white; border-color: #CE2D1E;display: block;
            margin-bottom: 10px;}
        /* Profile Modal Styling */
.profile-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    background-color: white;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    border-radius: 10px;
    padding: 20px;
    z-index: 1000;
    text-align: center;
}

.profile-content h2 {
    margin-top: 0;
    color: #CE2D1E;
}

.profile-content input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.profile-content button {
    margin: 5px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px;
    cursor: pointer;
    width: 45%;
}

/* Scheduler Styling */
#scheduler {
    background-color: #f9f9f9;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 20px auto;
}

#scheduler h3 {
    color: #CE2D1E;
    margin-bottom: 10px;
    text-align: center;
}

#scheduler input, #scheduler select {
    width: calc(100% - 20px);
    margin: 10px 0;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

#scheduleButton {
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    cursor: pointer;
    display: block;
    margin: 10px auto;
}

/* Close button styling */
.close {
    background-color: #CE2D1E;
    border: none;
    color: white;
    font-size: 16px;
    position: absolute;
    top: 5px;
    right: 10px;
    cursor: pointer;
}
.schedule {font-size: 12px;}
footer {
    background: #CE2D1E;
    color: white;
    text-align: center;
    padding: 20px;
    position: relative;
    bottom: 0;
    width: 100%;
    font-family: Arial, sans-serif;
}


    </style>
</head>
<body>
<div class="header">
    <button class="More" onclick="toggleMenu()">☰</button>
    <h1>Welcome to Tolos's Driving School!</h1>
    <div class="login-icon"></div>
    <button class="LogOff" onclick="logOff()">LogOff</button>
</div>
<div class="sidebar" id="menu" class="hidden">
    <h2>Menu</h2>
    <button class="button" onclick="showProfile()">View Profile</button>
    <div id="profileModal" class="profile-modal hidden">
    <div class="profile-content">
        <h2>Edit Profile</h2>
        <form id="profileForm" method="POST" action="update_profile.php">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($studentProfile['name']); ?>" required>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($studentProfile['email']); ?>" required>
            <button type="submit" class="button">Update Profile</button>
            <button type="button" class="button" onclick="closeProfile()">Cancel</button>
        </form>
    </div>
</div>
<div id="scheduler" class="hidden">
    <h3>Scheduler</h3>
    <input type="datetime-local" id="lessonDate" required>
    <select id="registeredCourses" required>
        <option value="">Select a Course</option>
        <?php
        foreach ($registeredCourses as $course) {
            echo '<option value="' . $course['id'] . '">' . htmlspecialchars($course['name']) . '</option>';
        }
        ?>
    </select>
    <select id="instructorSelect" required>
        <option value="">Select an Instructor</option>
        <!-- Instructors will be populated based on the selected course -->
    </select>
    <button class="button" id="scheduleButton">Schedule Lesson</button>
</div>
<button class="button" onclick="toggleScheduler()">Toggle Scheduler</button>
<h2 style="color: #ce2d1e; font-family: Arial, sans-serif; font-size: 20px;">Your Scheduled Lessons</h2>
<div class="schedule">
    <?php
    if (count($scheduledLessons) > 0) {
        foreach ($scheduledLessons as $lesson) {
            echo '<div class="course">';
            echo '<h3>' . htmlspecialchars($lesson['course_name']) . '</h3>';
            echo '<p>Instructor: ' . htmlspecialchars($lesson['instructor_name']) . '</p>';
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
</div>
</div>
<div class="content">
    <div style="position: relative;">
        <button class="cart-button" id="cartButton" onclick="toggleCart()">
            <div class="cart-icon"></div>
            Cart (0)
        </button>
        <div class="course-list" id="courseList">
            <button class="close" id="closebtn" onclick="closetoggleCart()">❌</button>
            <h4>Courses:</h4>
            <div id="cartItems"></div>
            <div class="total-price" id="totalPrice">🛒Total: R0</div>
            <button class="clear-Cart" id="clearCart">clear Cart</button>
            <button class="Check-out" id="checkout" onclick="Checkout()">Checkout</button>
        </div>
<div class="content">
<h1 class="student">Hi <?php echo htmlspecialchars($studentName); ?>!</h1>
<h2>Your Registered Courses</h2>
        <div class="coursesMain">
            <?php
            if (count($registeredCourses) > 0) {
                foreach ($registeredCourses as $course) {
                    echo '<div class="course">';
                    echo '<img src="' . htmlspecialchars($course['imageurl']) . '" alt="' . htmlspecialchars($course['name']) . '">'; // Assuming there's an image URL
                    echo '<h3>' . htmlspecialchars($course['name']) . '</h3>';
                    echo '<p>' . htmlspecialchars($course['description']) . '</p>';
                    echo '<p>R ' . number_format($course['price'], 2) . '</p>';
                    echo '<button class="delete-course" onclick="deleteCourse(' . $course['id'] . ')">Delete Course</button>'; // Delete button
                    echo '</div>';
                }
            } else {
                echo "<p>No courses yet.</p>";
            }
            ?>
        </div>
    <h2>Available Courses</h2>
    <div class="coursesMain">
        <?php
        // Fetch courses from the database
        $sql = "SELECT * FROM courses"; // Adjust table name if necessary
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data for each course
            while ($row = $result->fetch_assoc()) {
                echo '<div class="course">';
                echo '<img src="' . $row['imageurl'] . '" alt="' . $row['name'] . '">'; // Assuming you have an image URL column
                echo '<h3>' . $row['name'] . '</h3>';
                echo '<p>' . $row['description'] . '</p>';
                echo '<p>R ' . number_format($row['price'], 2) . '</p>';
                echo '<button class="add-to-cart" onclick="addToCart(\'' . $row['id'] . '\', \'' . $row['C_code'] . '\', \'' . addslashes($row['name']) . '\', ' . $row['price'] . ')">Add to Cart</button>';
                echo '</div>';
            }
        } else {
            echo "<p>No courses available.</p>";
        }

        // Close the connection
        $conn->close();
        ?>
    </div>

</div>

<script>
    function toggleMenu() {
        const menu = document.getElementById('menu');
        menu.classList.toggle('hidden');
    }
    function Checkout(){
    // Store the cart in session storage
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "store_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr.send(JSON.stringify(cart));

    // Redirect to the checkout page
    window.location.href = 'Checkoutpage.php';
}

    function showProfile() {
        const profileModal = document.getElementById('profileModal');
        profileModal.classList.remove('hidden');
    }

    function closeProfile() {
        const profileModal = document.getElementById('profileModal');
        profileModal.classList.add('hidden');
    }

    function toggleScheduler() {
        const scheduler = document.getElementById('scheduler');
        scheduler.classList.toggle('hidden');
    }

    let cart = [];
    let total = 0;

    function addToCart(courseId, courseCode, courseName, price) {
        const limitedCourseIds = [1, 2, 3]; // Define your limited course IDs
        const isLimitedCourse = limitedCourseIds.includes(parseInt(courseId)); // Check if the course ID is in the limited list

        if (isLimitedCourse && cart.some(item => item.id === courseId)) {
            alert("You can only purchase one of this course at a time.");
            return; // Exit if the limited course is already in the cart
        }

        const existingCourse = cart.find(item => item.id === courseId); // Check for existing course by ID

        if (existingCourse) {
            if (!isLimitedCourse && existingCourse.quantity < 25) {
                existingCourse.quantity += 1; // Increment quantity if course is not limited
            } else if (isLimitedCourse) {
                alert("You can only purchase one of this course at a time.");
                return; // Exit if trying to add more of a limited course
            } else {
                alert("You cannot add more than 25 of this course.");
                return; // Exit if trying to exceed the limit
            }
        } else {
            cart.push({ id: courseId, C_code: courseCode, name: courseName, price: price, quantity: 1 }); // Add new course
        }

        total += price; // Update total price
        console.log(cart); // Debugging statement
        updateCartDisplay();

        addToCartInDB(courseId, 1, price); // Send to DB
    }


function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    const totalPrice = document.getElementById('totalPrice');
    const cartButton = document.getElementById('cartButton');

    // Display cart items
    cartItems.innerHTML = cart.map(item => `<p>${item.name} - R${item.price.toFixed(2)} (${item.quantity})</p>`).join('');

    // Calculate total item count and total price
    const totalItemCount = cart.reduce((count, item) => count + item.quantity, 0);
    const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    totalPrice.innerHTML = `Total: R${totalAmount.toFixed(2)}`;
    cartButton.textContent = `Cart (${totalItemCount})`; // Update cart button display
}



    function toggleCart() {
        const courseList = document.getElementById('courseList');
        courseList.classList.toggle('active');
    }

    function closetoggleCart() {
        const courseList = document.getElementById('courseList');
        courseList.classList.remove('active');
    }

    function clearCart() {
    // Clear cart in JavaScript
    cart = [];
    total = 0;
    updateCartDisplay();

    // Send AJAX request to clear the database cart
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "clearCart.php", true);
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr.send(JSON.stringify({ student_id: userId })); // Use the `userId` variable in JavaScript
}



    document.getElementById('clearCart').addEventListener('click', clearCart);

    function logOff() {
        window.location.href = 'Welcomepage.php';
    }
    const userId = <?php echo json_encode($userId); ?>;

    function addToCartInDB(courseId, quantity, totalAmount) {
    console.log(`Adding to cart - User ID: ${userId}, Course ID: ${courseId}, Quantity: ${quantity}, Total Amount: ${totalAmount}`);
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "Cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

    const data = {
        userId: userId,   // Assuming you have a variable `userId` in your code
        courseId: courseId,
        quantity: quantity,
        totalAmount: totalAmount
    };
    xhr.send(JSON.stringify(data));  // Send cart data as JSON to the PHP file

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log('Item added to cart in the database');
        }
    };
}
document.getElementById('registeredCourses').addEventListener('change', function() {
    const courseId = this.value;
    const instructorSelect = document.getElementById('instructorSelect');

    // Clear existing options
    instructorSelect.innerHTML = '<option value="">Select an Instructor</option>';

    if (courseId) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `fetch_instructors.php?course_id=${courseId}`, true); // Fetch instructors based on course ID
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const instructors = JSON.parse(xhr.responseText);
                instructors.forEach(instructors => {
                    const option = document.createElement('option');
                    option.value = instructors.id; // Instructor ID
                    option.textContent = instructors.name; // Instructor Name
                    instructorSelect.appendChild(option);
                });
            }
        };
        xhr.send();
    }
});
document.getElementById('scheduleButton').addEventListener('click', scheduleLesson);

function scheduleLesson() {
    const lessonDate = document.getElementById('lessonDate').value;
    const courseId = document.getElementById('registeredCourses').value;
    const instructorId = document.getElementById('instructorSelect').value; // Get selected instructor ID

    if (!lessonDate || !courseId || !instructorId) {
        alert("Please select a course, an instructor, and enter a lesson date.");
        return;
    }

    const duration = 60; // Duration in minutes
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "schedule_lesson.php", true);
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr.send(JSON.stringify({ studentId: userId, instructorId: instructorId, lessonDate, courseId, duration }));

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert('Lesson scheduled successfully!');
        } else if (xhr.readyState === 4) {
            alert('Failed to schedule lesson: ' + xhr.responseText);
        }
    };
}
function deleteCourse(courseId) {
    if (confirm("Are you sure you want to delete this course?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_course.php", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.send(JSON.stringify({ courseId: courseId }));

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    alert('Course deleted successfully!');
                    location.reload(); // Refresh the page to see the changes
                } else {
                    alert('Failed to delete course: ' + response.message);
                }
            }
        };
    }
}

</script>
<footer>
    <p>Founded: 2024</p>
    <p>Contact us: info@tolosdrivingschool.com</p>
    <p>Phone: 0817748530</p>

</footer>
</body>
</html>
