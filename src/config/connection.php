<?php
$dbHost ="localhost"; // Default host MySql
$dbPort = "3306"; // Default port MySql
$database = "renderco_enderdeploy"; // Database name change it in production
$dbCharset = "utf8mb4";
$dbUsername = "root"; // Default username MySql
$dbPassword = $_ENV['dbPassword'];
try {
    $connection = new PDO(
        "mysql:$dbHost=localhost;port=$dbPort;dbname=$database;charset=$dbCharset",
        "$dbUsername",
        "$dbPassword",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ]
    );
} catch (PDOException $e) {
    error_log('Connection Error: ' . $e->getMessage());
    echo "We can not connect to the database.";
    exit;
}
?>
