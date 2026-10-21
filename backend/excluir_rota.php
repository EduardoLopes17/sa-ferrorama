
<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../public/login.php');
    exit;
}

if (($_SESSION['perfil'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acesso negado.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

require_once __DIR__ . '/../infra/conexao.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    exit('Rota inválida.');
}

try {
    $stmt = $conexao->prepare("DELETE FROM rotas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: ../index.php?rota_excluida=sucesso');
    exit;

} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Não foi possível excluir a rota.');
}
