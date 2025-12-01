<?php
echo "Test MySQL Driver\n";
echo "PHP Version: " . phpversion() . "\n\n";

// Vérifier l'extension PDO MySQL
if (extension_loaded('pdo_mysql')) {
    echo "✅ pdo_mysql EXTENSION IS LOADED\n";
} else {
    echo "❌ pdo_mysql EXTENSION NOT LOADED\n";
}

// Vérifier toutes les extensions PDO
echo "\nPDO Drivers available:\n";
foreach (PDO::getAvailableDrivers() as $driver) {
    echo " - $driver\n";
}

// Tester la connexion
echo "\nTrying to connect to MySQL...\n";
try {
    $host = getenv('MYSQLHOST') ?: getenv('RAILWAY_PRIVATE_DOMAIN');
    $db = getenv('MYSQLDATABASE') ?: 'railway';
    $user = getenv('MYSQLUSER') ?: 'root';
    $pass = getenv('MYSQLPASSWORD');
    $port = getenv('MYSQLPORT') ?: 3306;
    
    echo "Host: $host\n";
    echo "Database: $db\n";
    echo "User: $user\n";
    echo "Port: $port\n";
    
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    echo "✅ MySQL CONNECTION SUCCESSFUL\n";
} catch (Exception $e) {
    echo "❌ MySQL CONNECTION FAILED: " . $e->getMessage() . "\n";
}