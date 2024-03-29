<?php

// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$database = "movie_hub";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize user inputs
function sanitizeInput($input)
{
    global $conn;
    // Remove whitespace from the beginning and end of the input
    $input = trim($input);
    // Prevent SQL injection
    $input = mysqli_real_escape_string($conn, $input);
    // Prevent XSS attacks
    $input = htmlspecialchars($input);
    return $input;
}
