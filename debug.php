<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "secure_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['create_db'])) {
        // Create database
        $sql = "CREATE DATABASE IF NOT EXISTS secure_db";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Database created successfully or already exists.');</script>";
        } else {
            echo "<script>alert('Error creating database: " . $conn->error . "');</script>";
        }
    }

    // Select database
    $conn->select_db($dbname);

    if (isset($_POST['create_tables'])) {
        // Create users table
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )";

        $sql2 = "CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50),
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE) {
            echo "<script>alert('Tables created successfully.');</script>";
        } else {
            echo "<script>alert('Error creating tables: " . $conn->error . "');</script>";
        }
    }

    if (isset($_POST['clear_data'])) {
        $conn->query("DELETE FROM users");
        $conn->query("DELETE FROM comments");
        echo "<script>alert('All entries in tables have been deleted.');</script>";
    }

    if (isset($_POST['destroy_db'])) {
        $sql = "DROP DATABASE secure_db";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Database destroyed successfully.');</script>";
        } else {
            echo "<script>alert('Error destroying database: " . $conn->error . "');</script>";
        }
    }

    if (isset($_POST['goto_index'])) {
        echo "<script>window.location.href='index.php';</script>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Page</title>
</head>
<body>
    <h2>Debug Actions</h2>
    <form method="POST">
        <button type="submit" name="create_db">Create Database</button>
        <button type="submit" name="create_tables">Create Tables</button>
        <button type="submit" name="goto_index">Go to Login Page</button>
        <button type="submit" name="clear_data">Remove All Entries</button>
        <button type="submit" name="destroy_db">Destroy Database</button>
    </form>
</body>
</html>
