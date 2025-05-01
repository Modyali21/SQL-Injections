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

// Set error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html>
<head>
    <title>LIKE Clause SQL Injection Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 5px; }
        .query-box { background: white; padding: 15px; border: 1px solid #ddd; margin: 10px 0; }
        .error { color: red; background: #fee; padding: 10px; border: 1px solid red; }
        .success { color: green; }
        form { margin: 20px 0; }
        input[type="text"] { padding: 8px; width: 300px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; }
        table td { padding: 6px; }
        table th { padding: 6px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>LIKE Clause SQL Injection</h1>
        <p>This page demonstrates LIKE Clause SQL injection vulnerabilities.</p>
        
        <form method="GET">
            <label>Enter Username Pattern: 
                <input type="text" name="username" value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
            </label>
            <button type="submit">Query</button>
        </form>

        <?php
        if (isset($_GET['username'])) {
            $username = $_GET['username'];
            
            // Vulnerable query
            $sql = "SELECT * FROM users WHERE username LIKE '$username%'";
            
            echo "<div class='query-box'>";
            echo "<h3>Executed Query:</h3>";
            echo "<code>" . htmlspecialchars($sql) . "</code>";
            echo "</div>";
            
            try {
                $result = $conn->query($sql);
                
                if ($result) {
                    echo "<table border='1'><tr><th>ID</th><th>Username</th><th>Password</th><th>Bio</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['bio']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p style='color:red'>Error: " . $conn->error . "</p>";
                }
            } catch (Exception $e) {
                echo "<div class='error'>";
                echo "<h3>Database Error:</h3>";
                echo "<p>" . $e->getMessage() . "</p>";
                echo "</div>";
            }
        }
        ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>

<!-- Input: %' OR '1'='1 -->
