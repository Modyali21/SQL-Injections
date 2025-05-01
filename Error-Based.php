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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Error-Based SQL Injection Demo</title>
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
        <h1>Error-Based SQL Injection Demo</h1>
        <p>Enter a user ID to search:</p>
        
        <form method="GET">
            <input type="text" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '1') ?>">
            <button type="submit">Search</button>
        </form>

        <?php
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // Vulnerable query
            $sql = "SELECT * FROM users WHERE id = '$id'";
            
            echo "<div class='query-box'>";
            echo "<h3>Executed Query:</h3>";
            echo "<code>" . htmlspecialchars($sql) . "</code>";
            echo "</div>";
            
            try {
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    echo "<div class='success'>";
                    echo "<h3>User Found:</h3>";
                    $row = $result->fetch_assoc();
                    echo "<pre>" . print_r($row, true) . "</pre>";
                    echo "</div>";
                } else {
                    echo "<p>No user found with that ID.</p>";
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

<!-- Error Input: 1' AND 0=CONVERT(int,(SELECT table_name FROM information_schema.tables WHERE table_schema=database() LIMIT 1)) -- -->
<!-- Correct Input: 1' AND 1=CONVERT((SELECT table_name FROM information_schema.tables WHERE table_schema=database() LIMIT 1), SIGNED) -- -->