<?php 
namespace App\Controllers;

// require __DIR__ ."/../Models/UserModel.php";
// require __DIR__ ."/../Models/TokenModel.php";

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use App\Models\{
    UserModel,
    TokenModel,
};
use PDO;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class UserController
{
    private $userModel;
    private $tokenModel;
    public function __construct()
    {
        $this->userModel = new UserModel;
        $this->tokenModel = new TokenModel;

    }
    public function registerUser($data)
    {
        /*===== VERIFICA SE OS CAMPOS OBRIGATÓRIOS ESTÃO PRESENTES =====*/
        $requiredFields = ['nameUser','lastnameUser','emailUser','passwordUser'];

        foreach ($requiredFields as $field)
        {
            if (!isset($data[$field]) || empty($data[$field]))
            {
                return $this->error('Insufficient values',401);
            }
        }
        $name = $data['nameUser'];
        $lastname = $data['lastnameUser'];
        $email = $data['emailUser'];
        $password = $data['passwordUser'];
        
        /*===== VERIFICA SE JÁ EXISTE ALGUM USUARIO COM TAL EMAIL =====*/
        $verifyEmail = $this->userModel->verifyEmail($email);
    
        if($verifyEmail == 0)
        {
            /*===== CADASTRA O USUARIO NO BANCO DE DADOS =====*/
            $response = $this->userModel->registerUser($name, $lastname, $email, $password);
            if ($response == true) {
                return $this->success('user successfully registered', null);
            }
            return $this->error('error when trying to register user', 401);
        }
        return $this->error('Email is already in use', 401);
    }
    public function loginUser($data)
    {
        /*===== VERIFICA SE OS CAMPOS OBRIGATÓRIOS ESTÃO PRESENTES =====*/
        $requiredFields = ['emailUser','passwordUser'];

        foreach ($requiredFields as $field)
        {
            if (!isset($data[$field]) || empty($data[$field]))
            {
                return $this->error('Insufficient values',401);
            }
        }
        $email = $data['emailUser'];
        $password = $data['passwordUser'];
        /*===== VERIFICA SE O USUARIO EXISTE NO BANCO DE DADOS =====*/
        $response = $this->userModel->loginUser($email, $password);

        if ($response->rowCount() === 1)
        {
            $user =  $response->fetch(PDO::FETCH_ASSOC);

            /*===== VERIFICA SE O USUARIO JÁ POSSUI UM TOKEN, CASO EXISTIR, RETORNA ELE AO USUARIO =====*/
            $veriftUser = $this->tokenModel->veriftUser($user['id_user']);

            if($veriftUser->rowCount() == 1)
            {
                $userToken = $veriftUser->fetch(PDO::FETCH_ASSOC);
                return $this->success('User logged in successfully, token reused', $userToken['token']);
            }
            /*===== GERA UM NOVO TOKEN E RETORNA AO USUARIO =====*/
            $payload = array(
                'user' => $user['id_user'],
                'exp' => time() + 86400,
                'iat' => time()
            );
            $token = JWT::encode($payload, $_ENV['KEY'], 'HS256');
            $registerToken = $this->tokenModel->createToken($user['id_user'], $token, (time() + 86400));
            if ($registerToken == true)
            {
                return $this->success('User successfully logged in', $token);
            }   
        }
        /*===== CASO O USUARIO NÃO EXISTA OU OCORRA UM ERRO NO PROCESSO =====*/
        return $this->error('Login failed', 401);
    }
    public function getData($token)
    {
         /*===== VERIFICA SE OS CAMPOS OBRIGATÓRIOS ESTÃO PRESENTES =====*/

        if (!isset($token) || empty($token))
        {
            return $this->error('Insufficient values',401);
        }
        /*===== VERIFICA SE O TOKEN É VALIDO =====*/
        $verifyToken = $this->tokenModel->verifyToken($token);
        
        if ($verifyToken->rowCount() == 1)
        {
            $tokenData = $verifyToken->fetch(PDO::FETCH_ASSOC);
            $userId = $tokenData['user_id'];
            
            /*===== BUSCA OS DADOS DO USUÁRIO ASSOCIADOS AO TOKEN =====*/
            $userData = $this->userModel->getData($userId);
            if ($userData) {
                return $this->success('Data returned successfully', $userData);
            }
        }
         /*===== CASO O TOKEN SEJA INVALIDO OU OCORRA ALGUM ERRO NO PROCESSO =====*/
        return $this->error('Data return failed',401);
    }
    public function destroyUser($token)
    {
        /*===== VERIFICA SE OS CAMPOS OBRIGATÓRIOS ESTÃO PRESENTES =====*/
        if (!isset($token) || empty($token))
        {
            return $this->error('Insufficient values',401);
        }
        /*===== VERIFICA SE O TOKEN É VALIDO =====*/
        $verifyToken = $this->tokenModel->verifyToken($token);
        if ($verifyToken->rowCount() == 1)
        {
            $tokenData = $verifyToken->fetch(PDO::FETCH_ASSOC);
            $userId = $tokenData['user_id'];
            /*===== BUSCA OS DADOS DO USUÁRIO ASSOCIADOS AO TOKEN =====*/
            $destroyUser = $this->userModel->destroyUser($userId);
            $destroyToken = $this->tokenModel->deleteToken($token);
            if ($destroyUser == true && $destroyToken == true) {
                return $this->success('User successfully destroyed', null);
            }
        }
           /*===== cASO O TOKEN SEJA INVALIDO OU OCORRA ALGUM ERRO NO PROCESSO =====*/
        return $this->error('destruction failed',401);
    }
    public function updateUser($token, $data)
    {
         /*===== VERIFICA SE OS CAMPOS OBRIGATÓRIOS ESTÃO PRESENTES =====*/
        $requiredFields = ['nameUser','lastnameUser','passwordUser'];

        foreach ($requiredFields as $field)
        {
            if (!isset($data[$field]) || empty($data[$field]))
            {
                return $this->error('Insufficient values',401);
            }
        }
        $name = $data['nameUser'];
        $lastname = $data['lastnameUser'];
        $password = $data['passwordUser'];
          /*===== VERIFICA SE O TOKEN É VALIDO =====*/
        $verifyToken = $this->tokenModel->verifyToken($token);
        if ($verifyToken->rowCount() == 1)
        {
            $tokenData = $verifyToken->fetch(PDO::FETCH_ASSOC);
            $userId = $tokenData['user_id'];

            /*===== ATUALIZA SE AS SENHAS SÃO IGUAIS =====*/
            $getData = $this->userModel->getData($userId);

            $storedPassword = $getData['password_user'];

            if ($password === $storedPassword)
            {
                /*===== ATUALIZA OS DADOS DO USUARIO =====*/
                $userUpdated  = $this->userModel->updateUser($userId, $name, $lastname);
                /*===== CASO ATUALIZAR, GERAR UM NOVO TOKEN =====*/
                if ($userUpdated  == true)
                {
                    $payload = [
                        'user' => $userId,
                        'exp' => time() + 86400,
                        'iat' => time()
                    ];
                    $tokenGenerate = JWT::encode($payload, $_ENV['KEY'], 'HS256');
                    $destroyToken = $this->tokenModel->deleteToken($token);
                    $registerToken = $this->tokenModel->createToken($userId, $tokenGenerate, (time() + 86400));

                    if ($registerToken == true && $destroyToken == true)
                    {
                        return $this->success('User successfully logged in', $tokenGenerate);
                    }
                    return $this->error('Error deleting and creating token, log in again',401);
                }
            }
            return $this->error('Passwords are different',401);
        }
        /*===== cASO O TOKEN SEJA INVALIDO OU OCORRA ALGUM ERRO NO PROCESSO =====*/
        return $this->error('Data return failed',401);
    }
    public function success($message, $data)
    {
        /*===== MENSAGEM DE RETORNO DE SUCESSO COM A MENSAGEM E OS DADOS =====*/
        echo json_encode(array(
            'error' => false,
            'message' => $message,
            'data' => $data,
        ));
    }
    public function error($message, $statusCode)
    { 
        /*===== MENSAGEM DE RETORNO DE ERRO COM A MENSAGEM DE ERRO =====*/
        echo json_encode(array(
            'error' => true,
            'message' => $message,
        ), $statusCode);
    }
}
?>