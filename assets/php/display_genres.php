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

// Check if selectedGenres array is set and not empty
if (isset($_POST['selectedGenres']) && !empty($_POST['selectedGenres'])) {
    // Prepare SQL statement for inserting genres
    $stmt = $conn->prepare("INSERT INTO user_genres (user_id, genre_id) VALUES (?, ?)");

    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("ii", $user_id, $genre_id);

        // Insert each selected genre into the database
        foreach ($_POST['selectedGenres'] as $genre_id) {
            // Check if the user already has this genre
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM user_genres WHERE user_id = ? AND genre_id = ?");
            $checkStmt->bind_param("ii", $user_id, $genre_id);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count == 0) {
                // Execute the prepared statement
                if ($stmt->execute()) {
                    echo '<script>alert("Preference Updated succesfully")</script>';
                } else {
                    // Handle database insertion error
                    echo "Error inserting genres: " . $conn->error;
                }
            } else {
                echo "User already has genre with ID: $genre_id";
            }
        }

        // Close statement
        $stmt->close();
    } else {
        // Handle SQL statement preparation error
        echo "Error preparing SQL statement: " . $conn->error;
    }
} else {
    // Handle case where no genres were selected
    echo "No genres selected!";
}

// Retrieve genres from the database
$query = "SELECT genre_id, genre_name FROM genres";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Display form with checkboxes for each genre
    echo '<form id="genreForm" action="insert_genres.php" method="POST">';
    while ($row = $result->fetch_assoc()) {
        $genreId = $row['genre_id'];
        $genreName = $row['genre_name'];
        echo '<div class="genres">';
        echo '<input type="checkbox" name="selectedGenres[]" value="' . $genreId . '" id="genre-' . $genreId . '">';
        echo '<label for="genre-' . $genreId . '">' . $genreName . '</label>';
        echo '</div>';
    }
    echo '<button type="submit">Submit</button>';
    echo '</form>';
} else {
    echo 'No genres found in the database.';
}

// Close connection
$conn->close();
