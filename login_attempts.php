<?php
// Função para verificar se o número de tentativas de login recentes é menor do que o limite permitido
function check_login_attempts($username, $conn) {
    $time_limit = 10; // Tempo limite em minutos
    $max_attempts = 5; // Número máximo de tentativas

    // Obtém a hora atual e a hora limite
    $current_time = date("Y-m-d H:i:s");
    $time_ago = date("Y-m-d H:i:s", strtotime("-{$time_limit} minutes"));

    // Conta o número de tentativas de login do usuário dentro do período de tempo especificado
    $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE username = ? AND attempt_time > ?");
    $stmt->execute([$username, $time_ago]);
    $result = $stmt->fetch();

    // Se o número de tentativas exceder o limite máximo, retorna falso para bloquear novas tentativas de login
    if ($result['attempts'] >= $max_attempts) {
        return false;
    }

    // Caso contrário, retorna verdadeiro para permitir novas tentativas de login
    return true;
}

// Função para registrar uma tentativa de login na tabela "login_attempts"
function log_login_attempt($username, $successful, $conn) {
    $attempt_time = date("Y-m-d H:i:s");

    // Insere os dados da tentativa de login na tabela "login_attempts"
    $stmt = $conn->prepare("INSERT INTO login_attempts (username, attempt_time, successful) VALUES (?, ?, ?)");
    $stmt->execute([$username, $attempt_time, $successful]);
}
?>
