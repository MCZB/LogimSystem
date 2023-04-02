<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'login_db');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "login_db";

        $this->connection = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance->connection;
    }
}

?>
