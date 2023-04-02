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

$error = "";

// Se a requisição for do tipo POST, atualiza os dados do usuário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados enviados pelo formulário
    $username = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = $_POST["password"];

    // Verifica se a senha digitada é a mesma do usuário atual
    if (password_verify($password, $user["password"])) {
        // Atualiza os dados do usuário no banco de dados
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);

        // Redireciona para a página de dashboard e encerra o script
        header("Location: dashboard.php");
        exit();
    } else {
        // Define uma mensagem de erro se a senha estiver incorreta
        $error = "Senha incorreta. Por favor, tente novamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <!-- Título da página -->
        <h1 class="mt-5">Editar Perfil</h1>
        <?php if ($error): ?>
            <!-- Exibe a mensagem de erro, se houver -->
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <form action="edit_profile.php" method="post">
            <div class="form-group">
                <label for="username">Nome de usuário:</label>
                <!-- Input para o nome de usuário -->
                <input type="text" class="form-control" name="username" id="username"
                    value="<?= htmlspecialchars($user["username"], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <!-- Input para o e-mail -->
                <input type="email" class="form-control" name="email" id="email"
                    value="<?= htmlspecialchars($user["email"], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label for="password">Digite sua senha para confirmar as alterações:</label>
                <!-- Input para a senha -->
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="form-group">
                <!-- Botão para enviar o formulário -->
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
        </form>
        <p class="mt-3"><a class="btn btn-secondary" href="dashboard.php">Voltar</a></p>
    </div>
</body>
</html>
