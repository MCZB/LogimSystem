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
        <h1 class="mt-5">Bem-vindo, <?= $user["username"] ?>!</h1>
        <p class="mt-3"><a class="btn btn-primary" href="logout.php">Sair</a></p>
        <p class="mt-3"><a class="btn btn-primary" href="edit_profile.php">Editar perfil</a></p>
    </div>
</body>
</html>

