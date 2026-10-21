
<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../index.php?erro_sensor=id');
    exit;
}

$sql = "DELETE FROM sensores WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: ../index.php?sensor_excluido=sucesso');
    exit;
}

$stmt->close();
header('Location: ../index.php?erro_sensor=excluir');
exit;
