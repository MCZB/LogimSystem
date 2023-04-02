<?php
// Define as variáveis com as informações de conexão ao banco de dados
$db_host     = 'localhost';
$db_name     = 'login_db'; // Substitua pelo nome do seu banco de dados
$db_user     = 'root'; // Substitua pelo nome de usuário do seu banco de dados
$db_password = ''; // Substitua pela senha do seu banco de dados

// Tenta criar uma conexão PDO com o banco de dados
try {
    // Define a string de conexão DSN com as informações de conexão
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
    $conn = new PDO($dsn, $db_user, $db_password);
    // Define o modo de erro para exceções
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Se ocorrer um erro ao conectar, exibe uma mensagem de erro e encerra o script
    die("Erro na conexão: " . $e->getMessage());
}

// Prepara a consulta SQL com parâmetros nomeados
$stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND password = :password");

// Vincula as variáveis $username e $password aos parâmetros da consulta
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);

// Define os valores das variáveis $username e $password
$username = "exampleuser";
$password = "examplepassword";

// Executa a consulta
$stmt->execute();

// Recupera o resultado da consulta
$result = $stmt->fetch();
?> 
