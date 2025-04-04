<?php
session_start(); // Start or resume a session to check for logged-in user

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.html"); // Redirect to login page if the user is not logged in
    exit(); // Stop further script execution
}

$servername = "localhost"; // MySQL server address
$username = "root"; // MySQL username (default for XAMPP)
$password = ""; // MySQL password (empty unless set)
$dbname = "secure_db"; // Database name

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Stop script if connection fails
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comment = $_POST['comment']; // Capture the submitted comment

    // Restrict characters in the comment (only letters, numbers, spaces, and punctuation)
    if (!preg_match("/^[a-zA-Z0-9 .,!?]+$/", $comment)) {
        die("Error: Invalid characters detected in the comment!"); // Stop script if invalid characters are found
    }

    // Prevent XSS (Cross-site Scripting) attacks by escaping special characters in the comment
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

    // Prepare and execute an SQL statement to insert the comment into the database
    $stmt = $conn->prepare("INSERT INTO comments (comment) VALUES (?)");
    $stmt->bind_param("s", $comment); // Bind the comment as a string
    $stmt->execute(); // Execute the prepared statement
    
    echo "Comment posted successfully!"; // Inform the user that the comment was posted
    $stmt->close(); // Close the prepared statement
}

// Fetch existing comments from the database
$result = $conn->query("SELECT comment FROM comments"); // Get all comments from the 'comments' table
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Section</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- Display the username of the logged-in user -->
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

        <h3>Leave a Comment</h3>
        <!-- Informational text for users about allowed characters in the comment -->
        <p><strong>Allowed characters:</strong> Letters (A-Z, a-z), numbers (0-9), spaces, periods (.), commas (,), exclamation marks (!), and question marks (?).</p>

        <!-- Comment submission form -->
        <form method="POST" action="comment.php">
            <!-- Textarea for entering comments -->
            <textarea name="comment" placeholder="Write your comment here..." required></textarea>
            <!-- Submit button to post the comment -->
            <input type="submit" value="Post Comment">
        </form>

        <h3>Comments:</h3>
        <!-- Display existing comments -->
        <ul>
            <?php
            // Loop through each comment from the database and display it
            while ($row = $result->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($row['comment']) . "</li>"; // Prevent XSS by escaping the comment
            }
            ?>
        </ul>

        <!-- Logout link -->
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
