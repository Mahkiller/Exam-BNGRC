<?php
class Database {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $dbname = 'ETU004082_4338_4433';  // Nom de ta base (en majuscule)
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname;charset=utf8",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}