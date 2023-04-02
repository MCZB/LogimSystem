<?php
session_start();
include 'login_attempts.php';

// Inicialize a variável de mensagem de erro como vazia
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    require_once "db_connection.php";

    $username = htmlspecialchars($_POST["username"], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST["password"], ENT_QUOTES, 'UTF-8');

    if (check_login_attempts($username, $conn)) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $result = $stmt->fetch();

        if ($result) {
            if (password_verify($password, $result["password"])) {
                $_SESSION["user_id"] = $result["id"];
                
                // Inserir registro de login no banco de dados
                $login_time = date("Y-m-d H:i:s");
                $user_ip = $_SERVER["REMOTE_ADDR"];
                $stmt = $conn->prepare("INSERT INTO login_history (user_id, login_time, user_ip) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION["user_id"], $login_time, $user_ip]);
                
                // Registrar tentativa de login bem-sucedida
                log_login_attempt($username, 1, $conn);

                header("Location: dashboard.php");
                exit();
            } else {
                // Registrar tentativa de login malsucedida
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
