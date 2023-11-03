<?php
namespace App\Models;
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
        $sql = "INSERT INTO tb_usuario( nome_usuario, sobrenome_usuario, email_usuario, senha_usuario) 
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
        $sql = "SELECT * FROM bd_api_teste.tb_usuario WHERE email_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->rowCount() ?: false;
    }
    public function loginUser($email, $password)
    {
        $sql = "SELECT * FROM bd_api_teste.tb_usuario WHERE email_usuario = ? AND senha_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(1, $email, PDO::PARAM_STR);
        $stmt->bindParam(2, $password, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt;
    }
    public function getData($id)
    {
        $sql = "SELECT * FROM bd_api_teste.tb_usuario WHERE id_usuario = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }
    public function updateUser($id, $name, $lastname, $password)
    {
        $sql = "UPDATE bd_api_teste.tb_usuario SET
            nome_usuario = :name,
            sobrenome_usuario = :lastname,
            senha_usuario = :password
            WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute() ? true : false;    
    }
    public function destroyUser($id)
    {
        $sql = "DELETE FROM bd_api_teste.tb_usuario WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute() ? true : false;
    }
}
?>