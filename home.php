<?php
session_start();
require 'back.php';

$posts = getPosts(); // Fetch all posts from all users
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
</head>
<body>
    <h2>All Experience Posts</h2>
    
    <!-- If user is logged in, show logout link -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php">My Dashboard</a> |
        <a href="back.php?logout=true">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> | 
        <a href="register.php">Register</a>
    <?php endif; ?>

    <h3>Recent Posts</h3>
    <?php foreach ($posts as $post): ?>
        <div>
            <p><strong><?php echo htmlspecialchars($post['username']); ?>:</strong></p>
            <p><?php echo htmlspecialchars($post['content']); ?></p>

            <?php if ($post['image']): ?>
                <img src="<?php echo $post['image']; ?>" width="200">
            <?php endif; ?>

            <?php if ($post['video']): ?>
                <video width="200" controls>
                    <source src="<?php echo $post['video']; ?>" type="video/mp4">
                </video>
            <?php endif; ?>
            
            <form action="back.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit" name="like_post">Like</button>
            </form>

            <form action="back.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="text" name="comment" placeholder="Write a comment..." required>
                <button type="submit" name="comment">Comment</button>
            </form>

            <hr>
        </div>
    <?php endforeach; ?>
</body>
</html>
