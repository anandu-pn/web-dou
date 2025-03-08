<?php
// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
$host = '192.168.137.48';
$user = 'root';
$password = 'yourpassword';
$database = 'experience_app';
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Secure User Registration
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (strlen($password) < 8) {
        die('Password must be at least 8 characters long.');
    }
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);
    $stmt->execute();
    header("Location: login.php");
    exit();
}

// Secure User Login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            header("Location: dashboard.php");
            exit();
        } else {
            die('Invalid credentials.');
        }
    } else {
        die('Invalid credentials.');
    }
}

// Logout User
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// CSRF Protection
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Secure Post Creation with Image & Video Upload
if (isset($_POST['create_post']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $user_id = $_SESSION['user_id'];
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $image = '';
    $video = '';
    
    // For a single file input named "media" (if used in dashboard.php)
    if (!empty($_FILES['media']['name'])) {
        $fileType = mime_content_type($_FILES['media']['tmp_name']);
        $fileName = basename($_FILES['media']['name']);
        $uploadPath = 'uploads/' . $fileName;
        
        if (strpos($fileType, 'image') !== false) {
            $image = $uploadPath;
        } elseif (strpos($fileType, 'video') !== false) {
            $video = $uploadPath;
        }
        
        move_uploaded_file($_FILES['media']['tmp_name'], $uploadPath);
    }
    
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image, video) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $content, $image, $video);
    $stmt->execute();
}

// Fetch All Posts with Like and Comment Counts
function getPosts() {
    global $conn;
    $query = "
        SELECT posts.id, users.username, posts.content, posts.image, posts.video, 
               (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
               (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.id DESC
    ";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch Logged-in User's Posts
function getMyPosts() {
    global $conn;
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id, content, image, video FROM posts WHERE user_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch Comments for a Specific Post
function getComments($post_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT users.username, comments.comment 
        FROM comments 
        JOIN users ON comments.user_id = users.id 
        WHERE comments.post_id = ? 
        ORDER BY comments.id DESC
    ");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Edit Post
if (isset($_POST['edit_post']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $post_id = $_POST['post_id'];
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ?");
    $stmt->bind_param("si", $content, $post_id);
    $stmt->execute();
}

// Delete Post
if (isset($_POST['delete_post']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
}

// Comment on Post
// 1. Check for 'submit_comment' to ensure the button name is unique
// 2. Use $_POST['comment'] for the actual comment text
if (isset($_POST['submit_comment']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $comment = trim($_POST['comment']); 
    if ($comment === '') {
        die('Cannot submit an empty comment.');
    }
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
    
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();
}

// Like Post
if (isset($_POST['like_post'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE post_id = post_id");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
}

// Follow User
if (isset($_POST['follow_user'])) {
    $followed_id = $_POST['followed_id'];
    $follower_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE follower_id = follower_id");
    $stmt->bind_param("ii", $follower_id, $followed_id);
    $stmt->execute();
}
?>
