<?php
session_start();
require 'back.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$posts = getMyPosts(); // Fetch only the logged-in user's posts
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        /* Updated styling: Removed flex centering for vertical alignment */
        body, html {
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #0078FF, #00C6FF);
            font-family: Arial, sans-serif;
            color: #333;
        }
        
        .dashboard-container {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            margin: 0 auto;
        }
        
        .navbar {
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            margin-bottom: 20px;
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
        
        .navbar .btn-nav {
            background: #0078FF;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
        }
        
        h2 {
            color: #0078FF;
            margin-top: 0;
        }
        
        .post-form {
            margin-top: 20px;
            text-align: left;
        }
        
        textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 2px solid #0078FF;
            border-radius: 8px;
            box-sizing: border-box;
        }
        
        .btn {
            background-color: #0078FF;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
            font-size: 16px;
        }
        
        .btn:hover {
            background-color: #005ecb;
        }
        
        .post {
            background-color: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: left;
        }
        
        .post img, .post video {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }
        
        .delete-btn {
            background-color: #ff4d4d;
            margin-top: 10px;
        }
        
        .delete-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Navigation Bar -->
        <div class="navbar">
            <a class="btn-nav" href="home.php">Home</a>
            <a class="btn-nav" href="back.php?logout=true">Logout</a>
        </div>

        <h2>Welcome to Your Dashboard</h2>

        <h3>Create a New Post</h3>
        <form class="post-form" action="back.php" method="POST" enctype="multipart/form-data">
            <textarea name="content" placeholder="Share your experience..." required></textarea>
            <input type="file" name="media" accept="image/*,video/*">
            <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
            <button type="submit" class="btn" name="create_post">Post</button>
        </form>

        <h3>My Posts</h3>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <p><strong><?php echo htmlspecialchars($post['content']); ?></strong></p>
                <?php if (!empty($post['image'])): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
                <?php endif; ?>
                <?php if (!empty($post['video'])): ?>
                    <video controls>
                        <source src="<?php echo htmlspecialchars($post['video']); ?>" type="video/mp4">
                    </video>
                <?php endif; ?>

                <!-- Delete Post Button -->
                <form action="back.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
                    <button type="submit" class="btn delete-btn" name="delete_post">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
