<?php
// Define as constantes com as informações de conexão ao banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'login_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Cria a classe Database para gerenciar a conexão ao banco de dados
class Database {
    private static $instance = null;
    private $connection;

    // O construtor é privado para evitar a criação de instâncias fora da classe
    private function __construct() {
        // Define as variáveis com as informações de conexão
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "login_db";

        // Cria a conexão PDO com o banco de dados e define o modo de erro
        $this->connection = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Retorna a instância da conexão com o banco de dados
    public static function getInstance() {
        // Se a instância ainda não foi criada, cria uma nova
        if (!self::$instance) {
            self::$instance = new Database();
        }

        // Retorna a conexão
        return self::$instance->connection;
    }
}
?>
