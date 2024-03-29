<?php
// Start session
session_start();

// Include the database configuration
require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("location: login.html");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Initialize an array to store genre data
$genre_data = array();

// Prepare SQL statement to fetch genre IDs associated with the logged-in user from user_genres table
$stmt = $conn->prepare("SELECT genre_id FROM user_genres WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

// Execute the statement
$stmt->execute();

// Bind the result variable
$stmt->bind_result($genre_id);

// Fetch the results
while ($stmt->fetch()) {
    // Store genre ID in the array
    $genre_ids[] = $genre_id;
}

// Close statement
$stmt->close();

// If genre IDs were found, fetch their names from genres table
if (!empty($genre_ids)) {
    // Prepare SQL statement to fetch genre names based on genre IDs
    $in_clause = implode(',', array_fill(0, count($genre_ids), '?'));
    $sql = "SELECT genre_id, genre_name FROM genres WHERE genre_id IN ($in_clause)";
    $stmt = $conn->prepare($sql);

    // Bind parameters dynamically
    $stmt->bind_param(str_repeat('i', count($genre_ids)), ...$genre_ids);

    // Execute the statement
    $stmt->execute();

    // Bind the result variables
    $stmt->bind_result($genre_id, $genre_name);

    // Fetch the results
    while ($stmt->fetch()) {
        // Store genre ID and name in the array
        $genre_data[$genre_id] = $genre_name;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();

// Set content type header to JSON
header('Content-Type: application/json');

// Convert the PHP array to a JSON string
$genre_data_json = json_encode($genre_data);

// Output the JSON string
echo $genre_data_json;
