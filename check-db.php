<?php
// check-db.php
try {
    $db = new PDO(
        'mysql:host=' . getenv('MYSQLHOST') . ';port=' . getenv('MYSQLPORT') . ';dbname=' . getenv('MYSQLDATABASE'),
        getenv('MYSQLUSER'),
        getenv('MYSQLPASSWORD')
    );
    echo "Connexion MySQL OK!";
    echo "\nExtensions chargées: " . implode(', ', get_loaded_extensions());
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}