<?php
namespace Routes;

use App\Controllers\UserController;

$UserController = new UserController();

$Method = $_SERVER['REQUEST_METHOD'];
$URL = $_SERVER['REQUEST_URI'];
$headerAuth = $_SERVER['HTTP_AUTHORIZATION'];

if ($Method === 'POST') {
    if ($URL === '/register') {
        $data = json_decode(file_get_contents('php://input'), true);
        $UserController->registerUser($data);
    } elseif ($URL === '/login') {
        $data = json_decode(file_get_contents('php://input'), true);
        $UserController->loginUser($data);
    } else {
        // Rota desconhecida
        http_response_code(404);
    }
} elseif ($Method === 'GET') {
    if ($URL === '/getuser' && strpos($headerAuth, 'Bearer ') === 0) {
        $token = substr($headerAuth, 7);
        $UserController->getData($token);
    } else {
        // Rota desconhecida
        http_response_code(404);
    }
} elseif ($Method === 'DELETE' && $URL === '/delete' && strpos($headerAuth, 'Bearer ') === 0) {
    $token = substr($headerAuth, 7);
    $UserController->destroyUser($token);
} elseif ($Method === 'PUT' && $URL === '/update' && strpos($headerAuth, 'Bearer ') === 0) {
    $token = substr($headerAuth, 7);
    $data = json_decode(file_get_contents('php://input'), true);
    $UserController->updateUser($token, $data);
} else {
    // Rota desconhecida
    http_response_code(404);
}