<?php
// Inicia uma nova sessão ou retoma a sessão existente
session_start();

// Destrói a sessão atual, removendo todas as variáveis de sessão
session_destroy();

// Redireciona o usuário para a página de login
header("Location: login.php");
?>
