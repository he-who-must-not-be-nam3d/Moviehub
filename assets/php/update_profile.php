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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete all genres associated with the user
    $deleteStmt = $conn->prepare("DELETE FROM user_genres WHERE user_id = ?");
    $deleteStmt->bind_param("i", $user_id);
    if (!$deleteStmt->execute()) {
        // Handle deletion error
        echo "Error deleting old genres: " . $conn->error;
        exit();
    }
    $deleteStmt->close();

    // Check if selectedGenres array is set and not empty
    if (isset($_POST['selectedGenres']) && !empty($_POST['selectedGenres'])) {
        // Prepare SQL statement for inserting genres
        $insertStmt = $conn->prepare("INSERT INTO user_genres (user_id, genre_id) VALUES (?, ?)");
        if ($insertStmt) {
            // Bind parameters
            $insertStmt->bind_param("ii", $user_id, $genre_id);

            // Insert each selected genre into the database
            foreach ($_POST['selectedGenres'] as $genre_id) {
                // Execute the prepared statement
                if (!$insertStmt->execute()) {
                    // Handle database insertion error
                    echo "Error inserting genres: " . $conn->error;
                    exit();
                }
            }
            $insertStmt->close();
            // Redirect to user profile after successful insertion
            header("Location: /moviehub/home.html");
            exit();
        } else {
            // Handle SQL statement preparation error
            echo "Error preparing SQL statement: " . $conn->error;
            exit();
        }
    } else {
        // Handle case where no genres were selected
        echo "No genres selected!";
        exit();
    }
}

// Retrieve genres from the database
$query = "SELECT genre_id, genre_name FROM genres";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Profile</title>
    <!-- Include your CSS file if needed -->
    <link rel="stylesheet" href="../css/style.css" />
    <style>
        button {
            display: block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .profile-form {
            max-width: 600px;
            margin: 0 auto;
            background-color: grey;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .genre-checkbox {
            margin-bottom: 10px;
        }

        .error {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <center>
        <h1>User Profile</h1>
        <div class="profile-form">
            <form id="genreForm" action="" method="POST">
                <?php
                if ($result->num_rows > 0) {
                    // Display form with checkboxes for each genre
                    while ($row = $result->fetch_assoc()) {
                        $genreId = $row['genre_id'];
                        $genreName = $row['genre_name'];
                        echo '<div class="genre-checkbox">';
                        echo '<input type="checkbox" name="selectedGenres[]" value="' . $genreId . '" id="genre-' . $genreId . '">';
                        echo '<label for="genre-' . $genreId . '">' . $genreName . '</label>';
                        echo '</div>';
                    }
                    echo '<button type="submit">Submit</button>';
                } else {
                    echo 'No genres found in the database.';
                }
                ?>
            </form>
        </div>
    </center>
</body>

</html>