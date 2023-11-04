<?php
namespace App\Models;
require __DIR__ ."/../../config/database.php";
use Config\DatabaseConfig;
use PDO;
class UserModel 
{
    private $pdo;
    public function __construct() 
    {
        $dbconfig = new DatabaseConfig;
        $this->pdo = $dbconfig->pdo;
    }
    public function registerUser($name, $lastname, $email, $password)
    {
        $sql = "INSERT INTO bd_api_teste_2.tb_user( name_user, lastname_user, email_user, password_user) 
       VALUES (?, ?, ?, ?);";
       $stmt = $this->pdo->prepare($sql);
       $stmt->bindParam(1, $name, PDO::PARAM_STR);
       $stmt->bindParam(2, $lastname, PDO::PARAM_STR);
       $stmt->bindParam(3, $email, PDO::PARAM_STR);
       $stmt->bindParam(4, $password, PDO::PARAM_STR);

       return  $stmt->execute() ? true : false;
    }
    public function verifyEmail($email)
    {
        $sql = "SELECT * FROM bd_api_teste_2.tb_user WHERE email_user = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount() ?: false;
    }
    public function loginUser($email, $password)
    {
        $sql = "SELECT * FROM bd_api_teste_2.tb_user WHERE email_user = ? AND password_user = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->bindParam(2, $password, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;
    }
    public function getData($id)
    {
        $sql = "SELECT * FROM bd_api_teste_2.tb_user WHERE id_user = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }
    public function updateUser($id, $name, $lastname)
    {
        $sql = "UPDATE bd_api_teste_2.tb_user SET
            name_user = :name,
            lastname_user = :lastname,
            WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute() ? true : false;    
    }
    public function destroyUser($id)
    {
        $sql = "DELETE FROM bd_api_teste_2.tb_user WHERE id_user = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute() ? true : false;
    }
}
?>