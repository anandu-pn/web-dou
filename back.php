<?php
// Database Connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'experience_app';
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

session_start();

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
}

// CSRF Protection
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Secure Post Creation with Image & Video Upload
if (isset($_POST['create_post']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $user_id = $_SESSION['user_id'];
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $image = '';
    $video = '';
    
    if (!empty($_FILES['image']['name'])) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }
    
    if (!empty($_FILES['video']['name'])) {
        $video = 'uploads/' . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], $video);
    }
    
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image, video) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $content, $image, $video);
    $stmt->execute();
}

// Fetch All Posts
function getPosts() {
    global $conn;
    $result = $conn->query("SELECT posts.id, users.username, posts.content, posts.image, posts.video FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC");
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

// Edit Post
if (isset($_POST['edit_post']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $post_id = $_POST['post_id'];
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ?");
    $stmt->bind_param("si", $content, $post_id);
    $stmt->execute();
}

// Delete Post
if (isset($_POST['delete_post']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Ensure only the owner can delete their post
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
}

// Comment on Post
if (isset($_POST['comment']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
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
