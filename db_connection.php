<?php

$db_host     = 'localhost';
$db_name     = 'login_db'; // Substitua pelo nome do seu banco de dados
$db_user     = 'root'; // Substitua pelo nome de usuário do seu banco de dados
$db_password = ''; // Substitua pela senha do seu banco de dados

try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
    $conn = new PDO($dsn, $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Para parametrizar os dados de entrada e executar uma consulta
$stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);

// Definir parâmetros e executar a consulta
$username = "exampleuser";
$password = "examplepassword";
$stmt->execute();

// Recupere o resultado
$result = $stmt->fetch();

?> 