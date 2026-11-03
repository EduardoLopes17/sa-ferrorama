<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql_verificar = "SELECT id FROM trens WHERE usuario_responsavel_id = ? LIMIT 1";

$stmt_verificar = $conexao->prepare($sql_verificar);
$stmt_verificar->bind_param("i", $id);
$stmt_verificar->execute();
$stmt_verificar->store_result();

if ($stmt_verificar->num_rows > 0) {
    $stmt_verificar->close();

    header("Location: ../index.php?erro_usuario=responsavel_trem");
    exit;
}

$stmt_verificar->close();

    $sql = "DELETE FROM usuarios WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        header("Location: ../index.php");
        exit;

    } else {

        echo "Erro ao excluir usuário: " . $conexao->error;

    }

} else {

    echo "Usuário não informado.";

}
?>