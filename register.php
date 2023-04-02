<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST["submit"])) {
    require_once "db_config.php";
    require_once "db_config.php";

    $username = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    $confirm_password = htmlspecialchars($_POST["confirm_password"]);

    // Verifica se a senha e a confirmação de senha são iguais
    if ($password !== $confirm_password) {
        $error_message = "A senha e a confirmação de senha não coincidem.";
    }

    // Verifica se o email é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "O endereço de e-mail é inválido.";
    }

    // Verifica se a senha é forte o suficiente
    if (strlen($password) < 8 || !preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $password)) {
        $error_message = "A senha deve ter pelo menos 8 caracteres e conter pelo menos uma letra e um número.";
    }

    // Verifica se o nome de usuário já está sendo usado
    $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch();

    if ($result) {
        $error_message = "O nome de usuário já está em uso.";
    }

    // Verifica se o email já está sendo usado
    $stmt = Database::getInstance()->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch();

    if ($result) {
        $error_message = "O endereço de e-mail já está em uso.";
    }

    // Insere o usuário no banco de dados
    if (!isset($error_message)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = Database::getInstance()->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        if ($stmt->rowCount() > 0) {
            header("Location: registration_success.php");
            exit();
        } else {
            $error_message = "Erro ao cadastrar usuário: " . $stmt->errorInfo()[2];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1 class="display-4">Registrar</h1>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger mt-4"><?= $error_message ?></div>
        <?php endif ?>
        <form class="my-4" method="post">
            <div class="form-group">
                <label for="username" class="form-label">Nome de usuário:</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Endereço de e-mail:</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Senha:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirme sua senha:</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Registrar</button>
        </form>
    </div>
</body>
</html>
