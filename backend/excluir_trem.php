
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

require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../index.php?erro_trem=id');
    exit;
}

$sql = "DELETE FROM trens WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

try {
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();

        header('Location: ../index.php?trem_excluido=sucesso');
        exit;
    }

    $stmt->close();

    header('Location: ../index.php?erro_trem=nao_encontrado');
    exit;

} catch (mysqli_sql_exception $e) {
    $stmt->close();

    header('Location: ../index.php?erro_trem=excluir');
    exit;
}
