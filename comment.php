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
    echo "<script>alert('Error: Invalid characters detected in the comment!'); window.location.href='comment.php';</script>";
    exit();// Stop script if invalid characters are found
    }

    // ✅ Add length check (250 characters max)
    if (strlen($comment) > 250) {
        echo "<script>alert('Error: Comment must be 250 characters or less.'); window.location.href='comment.php';</script>";
        exit();
    }

    // Prevent XSS (Cross-site Scripting) attacks by escaping special characters in the comment
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

    // Prepare and execute an SQL statement to insert the comment into the database
    $stmt = $conn->prepare("INSERT INTO comments (username, comment) VALUES (?, ?)");
    $stmt->bind_param("ss", $_SESSION['username'], $comment);
    $stmt->execute(); // Execute the prepared statement

    echo "<script>alert('Comment posted successfully!'); window.location.href='comment.php';</script>";// Inform the user that the comment was posted
    exit();
    $stmt->close(); // Close the prepared statement
}

// Pagination setup
$commentsPerPage = 2;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $commentsPerPage;

function wrapTextWithDash($text, $width = 25) {
    $wrapped = '';
    $length = strlen($text);
    $i = 0;

    while ($i < $length) {
        $chunk = substr($text, $i, $width);

        // If the chunk breaks a word
        if (
            $i + $width < $length &&
            ctype_alpha($text[$i + $width - 1]) &&
            ctype_alpha($text[$i + $width])
        ) {
            $wrapped .= $chunk . "-\n";
        } else {
            $wrapped .= $chunk . "\n";
        }

        $i += $width;
    }

    return $wrapped;
}

// Count total comments
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM comments");
$totalRow = $totalResult->fetch_assoc();
$totalComments = $totalRow['total'];
$totalPages = ceil($totalComments / $commentsPerPage);

// Fetch comments for current page
$sql = "SELECT username, comment, created_at FROM comments ORDER BY created_at DESC LIMIT $commentsPerPage OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Section</title>
    <link rel="stylesheet" href="style.css">
</head>
<body id="comment">
    <div class="container">
        <!-- Display the username of the logged-in user -->
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>

        <h3>Leave a Comment</h3>
        <!-- Informational text for users about allowed characters in the comment -->
        <p><strong>Allowed characters:</strong> Letters (A-Z, a-z), numbers (0-9), spaces, periods (.), commas (,), exclamation marks (!), and question marks (?).</p>

        <form method="POST" action="comment.php" id="commentForm">
          <div class="textarea-wrapper">
            <textarea name="comment" id="commentInput" placeholder="Write your comment here..." maxlength="250" required></textarea>
            <p id="charCount">0 / 250</p>
          </div>
          <input type="submit" value="Post Comment" id="comment">
        </form>

        <script>
          const input = document.getElementById('commentInput');
          const counter = document.getElementById('charCount');
          const form = document.getElementById('commentForm');

          const allowedPattern = /^[A-Za-z0-9 .,!?]*$/;

          input.addEventListener('input', function () {
            counter.textContent = `${input.value.length} / 250`;
          });

          form.addEventListener('submit', function (e) {
            if (!allowedPattern.test(input.value)) {
              e.preventDefault();
              alert('Comment contains invalid characters. Only letters, numbers, spaces, and . , ! ? are allowed.');
            }
          });
        </script>


        <script>
        document.getElementById("commentInput").addEventListener("keydown", function(event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault(); // Prevent newline
                document.getElementById("commentForm").submit(); // Submit the form
            }
        });
        </script>

        <h3>Comments:</h3>
        <ul>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<li><strong>" . htmlspecialchars($row['username']) . "</strong> (" . $row['created_at'] . "):<br>";
            echo nl2br(htmlspecialchars(wrapTextWithDash($row['comment'], 25))) . "</li>";
        }
        ?>
        </ul>

        <!-- Pagination controls -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">« Prev</a>
            <?php endif; ?>

            <?php
            $maxButtons = 4; // Max buttons to show
            $startPage = max(1, $page - floor($maxButtons / 2));
            $endPage = $startPage + $maxButtons - 1;

            if ($endPage > $totalPages) {
                $endPage = $totalPages;
                $startPage = max(1, $endPage - $maxButtons + 1);
            }

            for ($i = $startPage; $i <= $endPage; $i++): ?>
              <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active-page' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>


            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Next »</a>
            <?php endif; ?>
            <div style="height: 10px;"></div>
            <hr>
        </div>

        <!-- Logout link -->
        <a href="logout.php" id="logout">Logout</a>
    </div>
</body>
</html>
