<?php
// Database connection details
$servername = "localhost"; // MySQL server address
$username = "root"; // MySQL username (default is 'root' for XAMPP)
$password = ""; // MySQL password (empty unless set)
$dbname = "secure_db"; // Database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Stop script if error in connecting to database
}

// Check if the form was submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the form inputs
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Restrict characters to letters, numbers, and underscores using regular expression
    if (!preg_match("/^[a-zA-Z0-9_]+$/", $user) || !preg_match("/^[a-zA-Z0-9_]+$/", $pass)) {
        die("Error: Invalid characters detected! Use only letters, numbers, and underscores.");
    }

    // Hash the password for security using bcrypt
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT); // Hashes the password before storing it

    // Prepare an SQL statement to insert data securely using prepared statements
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $user, $hashed_password); // Bind the username and hashed password to the statement

    // Execute the statement
    if ($stmt->execute()) {
        echo "Registration successful! <a href='index.html'>Login here</a>"; // If successful, show a success message with a login link
    } else {
        echo "Error: Username already taken."; // If the username already exists, show an error message
    }

    $stmt->close(); // Close the prepared statement
}

// Close the database connection
$conn->close();
?>
