
<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

$sql_verificar_rota = "SELECT rotas.id
    FROM trens
    INNER JOIN rotas ON trens.rota_id = rotas.id
    WHERE trens.id = ?
    AND rotas.status = 'Ativa'
    LIMIT 1";

$stmt_verificar = $conexao->prepare($sql_verificar_rota);
$stmt_verificar->bind_param("i", $id);
$stmt_verificar->execute();
$stmt_verificar->store_result();

if ($stmt_verificar->num_rows > 0) {
    $stmt_verificar->close();

    header('Location: ../index.php?erro_trem=rota_ativa');
    exit;
}

$sql = "DELETE FROM trens WHERE id = ?";
$stmt_verificar->close();

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
