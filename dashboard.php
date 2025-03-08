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
</head>
<body>
    <h2>Welcome to Your Dashboard</h2>
    <a href="back.php?logout=true">Logout</a>

    <h3>Create a New Post</h3>
    <form action="back.php" method="POST" enctype="multipart/form-data">
        <textarea name="content" placeholder="Share your experience..." required></textarea><br>
        <input type="file" name="image" accept="image/*"><br>
        <input type="file" name="video" accept="video/*"><br>
        <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
        <button type="submit" name="create_post">Post</button>
    </form>

    <h3>My Posts</h3>
    <?php foreach ($posts as $post): ?>
        <div>
            <p><strong><?php echo htmlspecialchars($post['content']); ?></strong></p>
            <?php if ($post['image']): ?>
                <img src="<?php echo $post['image']; ?>" width="200">
            <?php endif; ?>
            <?php if ($post['video']): ?>
                <video width="200" controls>
                    <source src="<?php echo $post['video']; ?>" type="video/mp4">
                </video>
            <?php endif; ?>

            <!-- Delete Post Button -->
            <form action="back.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrfToken(); ?>">
                <button type="submit" name="delete_post">Delete</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
