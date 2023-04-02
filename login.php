<?php
session_start();

// Inicialize a variável de mensagem de erro como vazia
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
    require_once "db_connection.php";

    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch();

    if ($result) {
        if (password_verify($password, $result["password"])) {
            $_SESSION["user_id"] = $result["id"];
            header("Location: dashboard.php");
            exit();
        }
    }

    $error_message = "Usuário ou senha incorretos.";
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
                <?= $error_message ?>
            </div>
        <?php endif ?>
        <p class="mt-4">Não tem uma conta? <a href="register.php">Cadastre-se</a></p>
        </form>
    </div>
</body>
</html>
