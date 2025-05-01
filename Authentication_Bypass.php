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

// Initialize variables
$message = '';
$userDetails = null;

// Process form data if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    
    // Vulnerable query - authentication bypass demonstration
    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $message = "<div style='color:green;'>Authentication Successful!</div>";
        $userDetails = $result->fetch_assoc();
    } else {
        $message = "<div style='color:red;'>Authentication Failed</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vulnerable Login</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 50px; 
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h2 { 
            text-align: center; 
            color: #333;
        }
        form { 
            margin-top: 20px;
        }
        input { 
            width: 93.5%; 
            padding: 10px; 
            margin: 8px 0; 
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button { 
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 12px; 
            width: 100%; 
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0069d9;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        .query {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            margin: 15px 0;
            overflow-x: auto;
        }
        .user-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        
        <?php if (!empty($message)): ?>
            <div class="result">
                <?= $message ?>
                <?php if (isset($userDetails)): ?>
                    <div class="query">
                        <strong>Executed Query:</strong><br>
                        SELECT * FROM users WHERE username = '<?= htmlspecialchars($user) ?>' AND password = '<?= htmlspecialchars($pass) ?>'
                    </div>
                    <div class="user-details">
                        <strong>User Details:</strong><br>
                        <pre><?= print_r($userDetails, true) ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>