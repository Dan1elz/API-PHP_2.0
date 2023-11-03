<?php 
namespace App\Controllers;
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
            $veriftUser = $this->tokenModel->veriftUser($user['id']);

            if($veriftUser->rowCount() == 1)
            {
                $userToken = $veriftUser->fetch(PDO::FETCH_ASSOC);
                return $this->success('User sucessfully logged in', $userToken['token']);
            }
            /*===== GERA UM NOVO TOKEN E RETORNA AO USUARIO =====*/
            $payload = array(
                'user' => $user['id_user'],
                'exp' => time() + 86400,
                'iat' => time()
            );
            $token = JWT::encode($payload, $_ENV['KEY'], 'HS256');
            $registerToken = $this->tokenModel->createToken($token, $user['id_user'], time());
            if ($registerToken == true)
            {
                return $this->success('User successfully logged in', $token);
            }   
        }
        /*===== CASO O USUARIO NÃO EXISTA OU OCORRA UM ERRO NO PROCESSO =====*/
        return $this->error('Login failed', 401);
    }
    public function getData($data)
    {
         /*===== VERIFICA SE OS CAMPOS OBRIGATÓRIOS ESTÃO PRESENTES =====*/

        if (!isset($data['token']) || empty($data['token']))
        {
            return $this->error('Insufficient values',401);
        }
        /*===== VERIFICA SE O TOKEN É VALIDO =====*/
        $token = $data['token'];
        $verifyToken = $this->tokenModel->verifyToken($token);
        if ($verifyToken->rowCount() == 1)
        {
            $tokenData = $verifyToken->fetch(PDO::FETCH_ASSOC);
            $userId = $tokenData['id'];

            /*===== BUSCA OS DADOS DO USUÁRIO ASSOCIADOS AO TOKEN =====*/
            $userData = $this->userModel->getData($userId);
            if ($userData) {
                return $this->success('Data returned successfully', $userData);
            }
        }
         /*===== cASO O TOKEN SEJA INVALIDO OU OCORRA ALGUM ERRO NO PROCESSO =====*/
        return $this->error('Data return failed',401);
    }
    public function destroyUser($data)
    {
        /*===== VERIFICA SE OS CAMPOS OBRIGATÓRIOS ESTÃO PRESENTES =====*/
        if (!isset($data['token']) || empty($data['token']))
        {
            return $this->error('Insufficient values',401);
        }
        /*===== VERIFICA SE O TOKEN É VALIDO =====*/
        $token = $data['token'];
        $verifyToken = $this->tokenModel->verifyToken($token);
        if ($verifyToken->rowCount() == 1)
        {
            $tokenData = $verifyToken->fetch(PDO::FETCH_ASSOC);
            $userId = $tokenData['id'];
            /*===== BUSCA OS DADOS DO USUÁRIO ASSOCIADOS AO TOKEN =====*/
            $userData = $this->userModel->destroyUser($userId);
            if ($userData == true) {
                return $this->success('User successfully destroyed', $userData);
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
            $userId = $tokenData['id'];
            /*===== ATUALIZA SE AS SENHAS SÃO IGUAIS =====*/
            $response = $this->userModel->getData($userId);
            $getData = $response->fetch(PDO::FETCH_ASSOC);
            $storedPassword = $getData['senha_usuario'];
            if (password_verify($password, $storedPassword))
            {
                /*===== ATUALIZA OS DADOS DO USUARIO =====*/
                $userUpdated  = $this->userModel->updateUser($userId, $name, $lastname);
                /*===== CASO ATUALIZAR, GERAR UM NOVO TOKEN =====*/
                if ($userUpdated  == true)
                {
                $payload = array(
                    'user' => $userId['id_user'],
                    'exp' => time() + 86400,
                    'iat' => time()
                );
                $token = JWT::encode($payload, $_ENV['KEY'], 'HS256');
                $registerToken = $this->tokenModel->createToken($token, $userId['id_user'], time());
                if ($registerToken == true)
                {
                    return $this->success('User successfully logged in', $token);
                }   
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
        return json_encode([
            'error' => false,
            'message' => $message,
            'data' => $data,
        ]);
    }
    public function error($message, $statusCode)
    { 
        /*===== MENSAGEM DE RETORNO DE ERRO COM A MENSAGEM DE ERRO =====*/
        return json_encode([
            'error' => true,
            'message' => $message,
        ], $statusCode);
    }
}
?>