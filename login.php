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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Apply User's Custom Styles */
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0078FF, #00C6FF);
            font-family: Arial, sans-serif;
            color: #333;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100vw;
            height: 100vh;
        }

        .login-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #0078FF;
        }

        .input-group {
            text-align: left;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #0078FF;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: #005ecb;
        }

        .login-btn {
            background-color: #0078FF;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background-color: #005ecb;
        }

        .register-link {
            margin-top: 15px;
            display: block;
            text-decoration: none;
            color: #0078FF;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>Login</h2>
        <form action="back.php" method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" name="login" class="login-btn">Login</button>
        </form>

        <a href="register.php" class="register-link">Don't have an account? Register here</a>
    </div>
</div>

</body>
</html>
