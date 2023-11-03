<?php
namespace Config;
use PDO, PDOException;
/*===== DEFINIR PARAMETROS PARA CONEXAO =====*/
class DatabaseConfig
{
    private $config = [
        'host' => 'localhost',
        'dbname' => 'db_api_teste',
        'username' => 'root',
        'password' => '',
    ];
    public $pdo;
    public function __construct()
    {
        $this->pdo = $this->Conection();
    }
    private function Conection()
    {
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']}";

        /*===== TENTAR UMA CONEXAO COM O BANCO VIA PDO =====*/
        try {
            $pdo = new PDO($dsn, $this->config['username'], $this->config['password']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $pdo;
        } catch (PDOException $err) {
            die("Database connection error: ". $err->getMessage());
        }
    }
    public function __destruct()
    {
        $this->pdo = null;
    }
}

?>