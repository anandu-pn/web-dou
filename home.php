<?php
session_start();
require 'back.php';

// Fetch posts from the database
$posts = getPosts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="User Posts - View and interact with engaging posts.">
    <title>User Posts</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico">
    <style>
        /* Apply User's Custom Style */
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: linear-gradient(135deg, #0078FF, #00C6FF);
            font-family: Arial, sans-serif;
            color: #333;
        }

        .navbar {
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar a, .navbar button {
            color: #0078FF;
            text-decoration: none;
            margin: 0 10px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .navbar .login-btn {
            background: #0078FF;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .post {
            border-bottom: 1px solid #ddd;
            padding: 15px;
        }

        .post img, .post video {
            max-width: 100%;
            height: auto;
            display: block;
            margin-top: 10px;
            border-radius: 8px;
        }

        .like-btn, .comment-btn {
            margin-top: 10px;
            padding: 10px;
            border: none;
            background: #0078FF;
            color: white;
            cursor: pointer;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 14px;
        }

        .like-btn:hover, .comment-btn:hover {
            background: #005ecb;
        }

        .logout {
            text-decoration: none;
            background: red;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
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
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
    <h1>Experiences</h1>
    <span id="links">
        <a href="#">Homepage</a>
        <a href="dashboard.php">My Posts</a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a class="logout" href="back.php?logout=true">Logout</a>
        <?php else: ?>
            <button class="login-btn" onclick="window.location.href='login.php'">Login</button>
        <?php endif; ?>
    </span>
</nav>

<!-- Posts Section -->
<div class="container">
    <h2>All Experience Posts</h2>

    <?php foreach ($posts as $post): ?>
        <div class="post">
            <p><strong><?php echo htmlspecialchars($post['username']); ?>:</strong></p>
            <p><?php echo htmlspecialchars($post['content']); ?></p>

            <?php if (!empty($post['image'])): ?>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
            <?php endif; ?>

            <?php if (!empty($post['video'])): ?>
                <video controls>
                    <source src="<?php echo htmlspecialchars($post['video']); ?>" type="video/mp4">
                </video>
            <?php endif; ?>

            <!-- Like Button -->
            <form action="back.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit" name="like_post" class="like-btn">Like</button>
            </form>

            <!-- Comment Form -->
            <form action="back.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="text" name="comment" placeholder="Write a comment..." required>
                <button type="submit" name="comment" class="comment-btn">Comment</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
