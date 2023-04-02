<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db_connection.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    if (password_verify($password, $user["password"])) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->execute([$username, $email, $user_id]);
        
        header("Location: dashboard.php");
        exit();
    } else {
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
        <h1 class="mt-5">Editar Perfil</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <form action="edit_profile.php" method="post">
            <div class="form-group">
                <label for="username">Nome de usuário:</label>
                <input type="text" class="form-control" name="username" id="username" value="<?= $user["username"] ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" class="form-control" name="email" id="email" value="<?= $user["email"] ?>">
            </div>
            <div class="form-group">
                <label for="password">Digite sua senha para confirmar as alterações:</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
        </form>
        <p class="mt-3"><a class="btn btn-secondary" href="dashboard.php">Voltar</a></p>
    </div>
</body>
</html>
