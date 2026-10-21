<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../public/login.php');
    exit;
}

if (($_SESSION['perfil'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acesso negado. Apenas administradores podem acessar esta funcionalidade.');
}