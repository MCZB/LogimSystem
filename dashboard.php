<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["user_id"])) {
    // Redireciona para a página de login e encerra o script
    header("Location: login.php");
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require_once "db_connection.php";

// Obtém o ID do usuário da sessão
$user_id = $_SESSION["user_id"];

// Busca os dados do usuário no banco de dados
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Se o usuário não existir, destroi a sessão e redireciona para a página de login
if (!$user) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <!-- Exibe o nome do usuário logado -->
        <h1 class="mt-5">Bem-vindo,
            <?= htmlspecialchars($user["username"], ENT_QUOTES, 'UTF-8') ?>!
        </h1>
        <!-- Botão para sair da sessão -->
        <p class="mt-3"><a class="btn btn-primary" href="logout.php">Sair</a></p>
        <!-- Botão para editar o perfil -->
        <p class="mt-3"><a class="btn btn-primary" href="edit_profile.php">Editar perfil</a></p>
    </div>
</body>

</html>
