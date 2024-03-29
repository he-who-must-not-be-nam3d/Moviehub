<?php
// Start a session to persist user login state
session_start();

// Include the database configuration
require_once 'config.php';
//Check whether user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to home.html if logged in
    header("Location: /moviehub/home.html");
    exit(); // Make sure to exit after redirection
}
// Define variables and initialize with empty values
$username = $password = '';
$username_err = $password_err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check input errors before querying the database
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password, first_login FROM users WHERE username = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if username exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password, $first_login);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct
                            if ($first_login == 1) {
                                // First time login, redirect to profile page
                                session_regenerate_id();
                                $_SESSION['user_id'] = $id;
                                header("location: profile.php");
                            } else {
                                // Not first time login, redirect to home page
                                session_regenerate_id();
                                $_SESSION['user_id'] = $id;
                                header("location: /moviehub/index.html");
                            }
                            exit();
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MovieHub</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.svg" type="image/x-icon" />
    <!-- Google font inks -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />
    <!-- Javascript links -->
    <script src="../js/global.js" defer></script>
    <script type="module" src="../js/detail.js"></script>
    <style>
        body {
            justify-content: center;
            align-items: center;
        }

        .error-message {
            color: red;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <a href="index.html">
            <img src="../images/logo.png" width="140" height="32" alt="MovieHub Home" />
        </a>
        <div class="search-box" search-box>
            <div class="search-wrapper" search-wrapper>
                <input type="text" name="search" aria-label="search movies" placeholder="Search any Movie..." class="search-field" autocomplete="off" search-field />
                <img src="../images/search.png" width="24" height="24" alt="Search" class="leading-icon" />
            </div>
            <button class="search-btn" search-toggler menu-close>
                <img src="../images/close.png" alt="close search box" width="24" height="24" />
            </button>
        </div>

        <button class="search-btn" search-toggler menu-close>
            <img src="../images/search.png" width="24" height="24" alt="open search box" />
        </button>
        <button class="menu-btn active" menu-btn menu-toggler>
            <img src="../images/menu.png" width="24" height="24" alt="open menu" class="menu" />
            <img src="../images/menu-close.png" alt="close menu" width="24" height="24" class="close" />
        </button>
    </header>
    <main>
        <!-- Sidebar -->
        <nav class="sidebar" sidebar></nav>
        <div class="overlay" overlay menu-toggler></div>

        <center>
            <div class="login-form">
                <!-- Login Form -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="error-message">
                        <?php echo isset($username_err) ? $username_err : ''; ?>
                        <?php echo isset($password_err) ? $password_err : ''; ?>
                    </div>
                    <input type="text" name="username" placeholder="Username" required />
                    <input type="password" name="password" placeholder="Password" required />
                    <button type="submit">Login</button>
                    <p>Not registered? <a href="register.php">Sign Up here</a></p>
                    <?php if (isset($username_err) || isset($password_err)) : ?>

                    <?php endif; ?>
                </form>
            </div>
        </center>
</body>
</main>

</html>