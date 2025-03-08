<?php
session_start();
require 'back.php'; // Include backend functions

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="back.php" method="POST">
        <input type="text" name="username" placeholder="Choose a Username" required><br>
        <input type="password" name="password" placeholder="Choose a Password" required><br>
        <button type="submit" name="register">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
