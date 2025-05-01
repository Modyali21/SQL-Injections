<?php
// Database configuration
$host = 'localhost';
$dbname = 'webapp_db';
$username = 'user_mohamed';
$password = 'mody_aly2020';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Function to register new user (vulnerable to second-order injection)
function registerUser($conn, $username, $password, $bio) {
    $stmt = $conn->prepare("INSERT INTO users (username, password, bio) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $bio);
    return $stmt->execute();
}

// Function to perform second-order SQLi
function forgetPass($conn, $username) {
    $result = mysqli_query($conn, "SELECT bio FROM users WHERE username = '$username'");
    $row = mysqli_fetch_assoc($result);
    $bio = $row['bio'];

    // Injected bio is reused here unsafely
    mysqli_query($conn, "UPDATE users SET password = '$bio' WHERE username = '$username'");
}

// Function to get the user's password (for verification)
function getUserPassword($conn, $username) {
    $result = mysqli_query($conn, "SELECT password FROM users WHERE username = '$username'");
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['password'] : false;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $reg_username = $_POST['reg_username'];
        $reg_password = $_POST['reg_password'];
        $reg_bio = $_POST['bio'];

        if (registerUser($conn, $reg_username, $reg_password, $reg_bio)) {
            $reg_success = "Registration successful!";
        } else {
            $reg_error = "Registration failed: " . $conn->error;
        }
    } elseif (isset($_POST['forget_pass'])) {
        $login_username = $_POST['login_username'];
        forgetPass($conn, $login_username);
        $new_pass = getUserPassword($conn, $login_username);
        $login_success = "Password updated! New password: <strong>$new_pass</strong>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Second-Order SQL Injection Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"], textarea {
            padding: 8px; width: 100%; box-sizing: border-box;
        }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; }
        .success { color: green; }
        .error { color: red; }
        .section { margin-bottom: 30px; border-bottom: 1px solid #ddd; padding-bottom: 20px; }
        code { background-color: #eee; padding: 2px 5px; border-radius: 3px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Second-Order SQL Injection Demo</h1>

        <div class="section">
            <h2>1. Registration (First Vulnerable Point)</h2>
            <form method="POST">
                <input type="hidden" name="register" value="1">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="reg_username" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="reg_password" required>
                </div>
                <div class="form-group">
                    <label>Bio (Injection Point):</label>
                    <textarea name="bio" rows="3"></textarea>
                </div>
                <button type="submit">Register</button>
            </form>
            <?php if (isset($reg_success)) echo "<p class='success'>$reg_success</p>"; ?>
            <?php if (isset($reg_error)) echo "<p class='error'>$reg_error</p>"; ?>

            <h3>Malicious Bio Example:</h3>
            <code>', password=(SELECT password FROM (SELECT password FROM users WHERE username='admin') AS x) --</code>
            <p>This will copy the admin’s password to your account during password reset.</p>
        </div>

        <div class="section">
            <h2>2. Forget Password (Second Vulnerable Point)</h2>
            <form method="POST">
                <input type="hidden" name="forget_pass" value="1">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="login_username" required>
                </div>
                <button type="submit">Submit</button>
            </form>
            <?php if (isset($login_success)) echo "<p class='success'>$login_success</p>"; ?>
            <?php if (isset($login_error)) echo "<p class='error'>$login_error</p>"; ?>
        </div>
    </div>
</body>
</html>
