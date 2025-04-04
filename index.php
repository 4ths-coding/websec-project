<?php
session_start();
$loginFailed = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // DB connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "secure_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($pass, $hashed_password)) {
            $_SESSION['username'] = $user;
            header("Location: comment.php");
            exit();
        } else {
            $loginFailed = true;
        }
    } else {
        $loginFailed = true;
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- HTML Starts Here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <p><strong>Allowed characters:</strong> Letters (A-Z, a-z), numbers (0-9), and underscores (_) only.</p>

    <!-- Show error alert if login fails -->
    <?php if ($loginFailed): ?>
        <script>alert("Invalid credentials!");</script>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
