<?php
$page_title = 'Installation';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install eMarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 50px 0; }
        .installer { max-width: 600px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <div class="installer">
            <h2 class="text-center mb-4">eMarket Installation</h2>
            
            <?php
            $host = 'localhost';
            $db_name = 'ecommerce_db';
            $db_user = 'root';
            $db_pass = '';
            
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $host = $_POST['host'];
                $db_name = $_POST['db_name'];
                $db_user = $_POST['db_user'];
                $db_pass = $_POST['db_pass'];
                
                try {
                    $conn = new PDO("mysql:host=$host", $db_user, $db_pass);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $conn->exec("CREATE DATABASE IF NOT EXISTS $db_name");
                    $conn->exec("USE $db_name");
                    
                    // Drop existing tables if they exist
                    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
                    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                    foreach($tables as $table) {
                        $conn->exec("DROP TABLE IF EXISTS $table");
                    }
                    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
                    
                    $sql = file_get_contents(__DIR__ . '/database.sql');
                    $conn->exec($sql);
                    
                    $config_content = "<?php
define('BASE_URL', 'http://localhost/AI/');
define('SITE_NAME', 'eMarket');
define('CURRENCY', '\$');

define('DB_HOST', '$host');
define('DB_NAME', '$db_name');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');

define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

session_start();

function redirect(\$url) {
    header('Location: ' . BASE_URL . \$url);
    exit;
}

function dd(\$data) {
    echo '<pre>';
    print_r(\$data);
    echo '</pre>';
    exit;
}

function sanitize(\$data) {
    return htmlspecialchars(trim(\$data), ENT_QUOTES, 'UTF-8');
}

function formatPrice(\$price) {
    return CURRENCY . number_format(\$price, 2);
}
";
                    
                    file_put_contents(__DIR__ . '/config/config.php', $config_content);
                    
                    echo '<div class="alert alert-success">Installation completed successfully!</div>';
                    echo '<p>Default admin credentials:</p>';
                    echo '<ul><li>Email: admin@emarket.com</li><li>Password: password</li></ul>';
                    echo '<a href="index.php" class="btn btn-primary">Go to Store</a>';
                    echo '<a href="views/admin/login.php" class="btn btn-outline">Admin Login</a>';
                    
                } catch(PDOException $e) {
                    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
                }
            } else {
            ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Database Host</label>
                    <input type="text" name="host" class="form-control" value="localhost" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database Name</label>
                    <input type="text" name="db_name" class="form-control" value="ecommerce_db" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database Username</label>
                    <input type="text" name="db_user" class="form-control" value="root" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Database Password</label>
                    <input type="password" name="db_pass" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-100">Install</button>
            </form>
            
            <?php } ?>
        </div>
    </div>
</body>
</html>
