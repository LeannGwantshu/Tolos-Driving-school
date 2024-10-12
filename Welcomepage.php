<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Tolos Driving School</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://i.ibb.co/vXvQQtr/bacgrond-App.webp');
            margin: 0;
            padding: 0; /* Remove default padding */
            display: flex; /* Use flexbox */
            flex-direction: column; /* Align items vertically */
            justify-content: center; /* Center vertically */
            align-items: center; /* Center horizontally */
            height: 100vh; /* Full viewport height */
            text-align: center; /* Center text */
            color: white; /* Change default text color to white */
        }
        h1 {
            color: white; /* Set h1 text color to white */
            font-size: 48px;
            margin: 20px 0;
        }
        .button-container {
            margin: 20px 0;
        }
        .login-button, .register-button {
            background-color: black;
            color: white;
            border: none;
            padding: 15px 30px;
            text-align: center;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
            transition: background-color 0.3s;
        }
        .login-button:hover, .register-button:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>

<h1>Welcome to Tolos Driving School!</h1>

<div class="button-container">
    <a href="login.php" class="login-button">Already Registered? Log In</a>
    <a href="Registration.php" class="register-button">Not Registered? Register Here</a>
</div>

</body>
</html>
