<?php
try {
    $connection = new PDO('mysql:host=localhost;dbname=enderdeploy', 'root', '');
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

// $host = getenv('host');
// $dbname = getenv('dbname');
// $user = getenv('user');
// $password = getenv('password');
// $connection = new PDO(
//     "mysql:host=$host;dbname=$dbname;charset=utf8", 
//     "$user", 
//     "$password", 
//     [
//         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//         PDO::MYSQL_ATTR_SSL_CA => '/path/to/ca-cert.pem', // Certificado CA
//         PDO::MYSQL_ATTR_SSL_CERT => '/path/to/client-cert.pem', // Certificado del cliente
//         PDO::MYSQL_ATTR_SSL_KEY => '/path/to/client-key.pem', // Clave privada
//     ]
// );
?>