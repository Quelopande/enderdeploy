<?php
// Not necesary because there's only one API key registered in this API version
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;
// use Firebase\JWT\ExpiredException;

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        methodGet($connection);
        break;
    case 'POST':
        methodPost($connection, $input);
        break;
    default:
        echo json_encode(['output' => 'You need to use GET or POST method']);
        break;
}

$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
$v0ApiKey = $_ENV['v0ApiKey'];

if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Acceso denegado: Token Bearer no proporcionado o formato inválido.']);
    exit();
}
return $matches[1];

if(hash_equals()){}
