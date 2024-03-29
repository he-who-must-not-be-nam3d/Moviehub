<?php

// Include the database configuration
require_once 'config.php';

// Define variables and initialize with empty values
$email = $username = $password = $password_verify = '';
$email_err = $username_err = $password_err = $password_verify_err = '';

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate password verification
    if (empty(trim($_POST["password_verify"]))) {
        $password_verify_err = "Please confirm password.";
    } else {
        $password_verify = trim($_POST["password_verify"]);
        if (empty($password_err) && ($password != $password_verify)) {
            $password_verify_err = "Password did not match.";
        }
    }

    // Check input errors before inserting into database
    if (empty($email_err) && empty($username_err) && empty($password_err) && empty($password_verify_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (email, username, password, first_login) VALUES (?, ?, ?, 1)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_email, $param_username, $param_password);

            // Set parameters
            $param_email = $email;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                echo '<script>alert("Registration successful. You can now log in."); window.location.href = "login.php";</script>';
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
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
    <title>MovieHub - Register</title>
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
                <!-- Registration Form -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type="email" name="email" placeholder="Email" required />
                    <?php echo isset($email_err) ? '<div class="error">' . $email_err . '</div>' : ''; ?>
                    <input type="text" name="username" placeholder="Username" required />
                    <?php echo isset($username_err) ? '<div class="error">' . $username_err . '</div>' : ''; ?>
                    <input type="password" name="password" placeholder="Password" required />
                    <?php echo isset($password_err) ? '<div class="error">' . $password_err . '</div>' : ''; ?>
                    <input type="password" name="password_verify" placeholder="Verify Password" required />
                    <?php echo isset($password_verify_err) ? '<div class="error">' . $password_verify_err . '</div>' : ''; ?>
                    <button type="submit">Register</button>
                    <p>Already registered? <a href="login.php">Login here</a></p>
                </form>
            </div>
        </center>
    </main>
</body>

</html>