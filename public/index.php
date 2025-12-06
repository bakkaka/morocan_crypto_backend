<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;

// CORS Headers - AJOUTÉ ICI
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Accept, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 3600');

// Répondre immédiatement aux OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit(0);
}

// DEBUG PostgreSQL - AJOUTÉ ICI
if (isset($_GET['debug_db'])) {
    $host = $_ENV['PGHOST'] ?? getenv('PGHOST') ?? 'non défini';
    $port = $_ENV['PGPORT'] ?? getenv('PGPORT') ?? '5432';
    $db   = $_ENV['PGDATABASE'] ?? getenv('PGDATABASE') ?? 'railway';
    $user = $_ENV['PGUSER'] ?? getenv('PGUSER') ?? 'postgres';
    
    echo "<h2>Debug PostgreSQL Connection</h2>";
    echo "PGHOST: $host<br>";
    echo "PGPORT: $port<br>";
    echo "PGDATABASE: $db<br>";
    echo "PGUSER: $user<br>";
    
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db";
        $password = $_ENV['PGPASSWORD'] ?? getenv('PGPASSWORD') ?? '';
        $pdo = new PDO($dsn, $user, $password);
        echo "<p style='color:green;'>✅ Connexion PostgreSQL OK</p>";
        
        // Test query
        $stmt = $pdo->query("SELECT version()");
        echo "PostgreSQL Version: " . $stmt->fetchColumn() . "<br>";
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ Erreur: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>Extensions chargées:</h3>";
    echo "pdo_pgsql: " . (extension_loaded('pdo_pgsql') ? '✅' : '❌') . "<br>";
    echo "pgsql: " . (extension_loaded('pgsql') ? '✅' : '❌') . "<br>";
    
    exit(0);
}

// Force production environment for Railway
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'prod';
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? '0';

// Activer le debug si besoin
if ($_SERVER['APP_DEBUG']) {
    umask(0000);
    Debug::enable();
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};