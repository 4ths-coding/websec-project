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

        try {
            // Prepare your statement and execute it
            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!'); window.location.href='index.php';</script>";
                $registrationSuccess = true;
            } else {
                if (mysqli_errno($conn) == 1062) {  // 1062 is the error code for duplicate entry
                    echo "<script>alert('Error: Username already taken.');</script>";
                } else {
                    echo "<script>alert('An error occurred.');</script>";
                }
            }
        } catch (mysqli_sql_exception $e) {
            // Catch any SQL errors here
            echo "<script>alert('Error: An unexpected error occurred.');</script>";
        } finally {
            $stmt->close();
        }
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
    <h2>Register</h2>
    <p><strong>Allowed characters:</strong> Letters (A-Z, a-z), numbers (0-9), and underscores (_) only.</p>

    <!-- Form shown only if registration not successful -->
    <?php if (!$registrationSuccess): ?>
    <form method="POST" action="register.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <div id="password-strength"></div> <!-- Patrick & Richard view div#password-strength -->
        <div id="strengthBar"></div>    <!-- Patrick & Richard view div#strengthBar -->
        <input type="submit" value="Register" disabled id="register">
    </form>
    <p>Already have an account? <a href="index.php">Login here</a></p>
    <?php endif; ?>
</div>
    <script src="script.js"></script> <!-- Patrick & Richard link to script.js -->
</body>
</html>
