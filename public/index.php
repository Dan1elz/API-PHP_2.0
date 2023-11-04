<?php 
namespace Routes;
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../libraries/composer.phar');

require __DIR__ . '/../libraries/vendor/autoload.php';

use App\Controllers\UserController;

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$headerAuth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

$controller = new UserController();

switch ($method) {
    case 'POST':
        if ($uri === '/register') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->registerUser($data);
        } elseif ($uri === '/login') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->loginUser($data);
        } else {
            $controller->error('Rota POST desconhecida', 404);
        }
        break;
    case 'GET':
        if ($uri === '/teste') {
            $controller->error('Rota GET desconhecida', 200);
        } elseif ($uri === '/getuser' && strpos($headerAuth, 'Bearer ') === 0) {
            $token = substr($headerAuth, 7);
            $controller->getData($token);
        } else {
            $controller->error('Rota GET desconhecida', 404);
        }
        break;
    case 'DELETE':
        if ($uri === '/delete' && strpos($headerAuth, 'Bearer ') === 0) {
            $token = substr($headerAuth, 7);
            $controller->destroyUser($token);
        } else {
            $controller->error('Rota DELETE desconhecida', 404);
        }
        break;
    case 'PUT':
        if ($uri === '/update' && strpos($headerAuth, 'Bearer ') === 0) {
            $token = substr($headerAuth, 7);
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->updateUser($token, $data);
        } else {
            $controller->error('Rota PUT desconhecida', 404);
        }
        break;
    default:
        $controller->error('Rota não encontrada', 404);
}
?>