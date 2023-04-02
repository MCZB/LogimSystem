<?php
// Inicia uma nova sessão ou retoma a sessão existente
session_start();

// Inclui o arquivo que contém funções para lidar com tentativas de login
include 'login_attempts.php';

// Inicializa a variável de mensagem de erro como vazia
$error_message = "";

// Verifica se o método da requisição é POST e se os campos de usuário e senha estão definidos
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    // Inclui o arquivo que contém a conexão com o banco de dados
    require_once "db_connection.php";

    // Remove caracteres especiais e converte caracteres aplicáveis em entidades HTML
    $username = htmlspecialchars($_POST["username"], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST["password"], ENT_QUOTES, 'UTF-8');

    // Verifica se o usuário ainda tem tentativas de login disponíveis
    if (check_login_attempts($username, $conn)) {
        // Prepara e executa uma consulta para selecionar o id e a senha do usuário com base no nome de usuário informado
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch();

        // Verifica se a consulta retornou algum resultado
        if ($result) {
            // Verifica se a senha informada confere com a senha armazenada no banco de dados
            if (password_verify($password, $result["password"])) {
                // Armazena o id do usuário na sessão
                $_SESSION["user_id"] = $result["id"];
                
                // Insere um registro de login no banco de dados
                $login_time = date("Y-m-d H:i:s");
                $user_ip = $_SERVER["REMOTE_ADDR"];
                $stmt = $conn->prepare("INSERT INTO login_history (user_id, login_time, user_ip) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION["user_id"], $login_time, $user_ip]);
                
                // Registra a tentativa de login bem-sucedida
                log_login_attempt($username, 1, $conn);

                // Redireciona o usuário para a página do painel
                header("Location: dashboard.php");
                exit();
            } else {
                // Registra a tentativa de login malsucedida
                log_login_attempt($username, 0, $conn);
                $error_message = "Usuário ou senha incorretos.";
            }
        } else {
            $error_message = "Usuário ou senha incorretos.";
        }
    } else {
        $error_message = "Você excedeu o número máximo de tentativas. Por favor, tente novamente mais tarde.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1 class="display-4">Login</h1>
        <form class="my-4" method="post">
        <div class="form-group">
            <label for="username">Nome de usuário:</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Senha:</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary" name="submit">Entrar</button>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($error_message)): ?>
            <div class="alert alert-danger mt-4">
                <?= htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif ?>
        <p class="mt-4">Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
        </form>
    </div>
</body>
</html>
