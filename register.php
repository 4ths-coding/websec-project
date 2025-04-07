<?php
// Start of PHP logic
$registrationSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    if (!preg_match("/^[a-zA-Z0-9_]+$/", $user) || !preg_match("/^[a-zA-Z0-9_]+$/", $pass)) {
        echo "<script>alert('Error: Invalid characters! Use only letters, numbers, and underscores.');</script>";
    } else {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $user, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='index.php';</script>";
            $registrationSuccess = true;
        } else {
            echo "<script>alert('Error: Username already taken.');</script>";
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!-- HTML Starts Here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>REGISTER</h2>
    <p><strong>Allowed characters:</strong> Letters (A-Z, a-z), numbers (0-9), and underscores (_) only.</p>

    <!-- Form shown only if registration not successful -->
    <?php if (!$registrationSuccess): ?>
    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="index.php">Login here</a></p>
    <?php endif; ?>
</div>
</body>
</html>
