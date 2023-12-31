<?php
namespace App\Models;
// require __DIR__ ."/../../config/database.php";
use Config\DatabaseConfig;
use PDO;
class TokenModel 
{
    private $pdo;
    public function __construct() 
    {
        $dbconfig = new DatabaseConfig;
        $this->pdo = $dbconfig->pdo;
    }
    public function createToken($userId, $token, $expirationTime)
    {
        $expirationTimeFormatted = date('Y-m-d H:i:s', $expirationTime);
        
        $sql = "INSERT INTO bd_api_teste_2.tokens(user_id, token, expiration_time) VALUES (:userId, :token, :expirationTime)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);
        $stmt->bindParam(":expirationTime", $expirationTimeFormatted, PDO::PARAM_STR);

        return  $stmt->execute() ? true : false;
    }
    public function veriftUser($userId)
    {
        $sql = "SELECT * FROM bd_api_teste_2.tokens WHERE user_id = :userId"; 
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt ? : false;
    }
    public function verifyToken($token)
    {
        $sql = "SELECT * FROM bd_api_teste_2.tokens WHERE token = :token";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt ? : false;
    }
    public function deleteToken($token)
    {
        $sql = "DELETE FROM bd_api_teste_2.tokens WHERE token = :token";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(":token", $token, PDO::PARAM_STR);
        $stmt->execute();
        
        return  $stmt->execute() ? true : false;
    }
}
?>