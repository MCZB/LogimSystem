<?php
function check_login_attempts($username, $conn) {
    $time_limit = 10; // Tempo limite em minutos
    $max_attempts = 5; // Número máximo de tentativas

    $current_time = date("Y-m-d H:i:s");
    $time_ago = date("Y-m-d H:i:s", strtotime("-{$time_limit} minutes"));

    // Contar tentativas de login recentes
    $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE username = ? AND attempt_time > ?");
    $stmt->execute([$username, $time_ago]);
    $result = $stmt->fetch();

    if ($result['attempts'] >= $max_attempts) {
        return false;
    }

    return true;
}

function log_login_attempt($username, $successful, $conn) {
    $attempt_time = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO login_attempts (username, attempt_time, successful) VALUES (?, ?, ?)");
    $stmt->execute([$username, $attempt_time, $successful]);
}
?>
