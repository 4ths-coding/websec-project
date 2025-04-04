<?php
session_start(); // Start a session to store user data across pages

// Database connection details
$servername = "localhost";
$username = "root"; // Default XAMPP/MySQL username
$password = ""; // Default XAMPP password (empty unless set)
$dbname = "secure_db"; // Name of the database

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Stop script if error
}

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the form inputs
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prepare an SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user); // "s" means string type
    $stmt->execute(); // Execute the statement
    $stmt->store_result(); // Store the result for later use

    // Check if any matching user was found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password); // Bind the result to a variable
        $stmt->fetch(); // Fetch the result from the database

        // Verify the entered password with the hashed password in DB
        if (password_verify($pass, $hashed_password)) {
            $_SESSION['username'] = $user; // Save username in session
            header("Location: comment.php"); // Redirect to comment page
            exit(); // Stop executing the script
        } else {
          echo "<script>alert('Invalid credentials!'); window.location.href='index.php';</script>";
          exit();// Wrong password
        }
    } else {
      echo "<script>alert('User not found!'); window.location.href='index.php';</script>";
      exit(); // Username not found
    }

    $stmt->close(); // Close the prepared statement
}

$conn->close(); // Close the database connection
?>
