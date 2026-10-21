
<?php
require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

$nome = trim($_POST['nome'] ?? '');
$localizacao = trim($_POST['localizacao'] ?? '');
$tipo_sensor = trim($_POST['tipo_sensor'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$status = trim($_POST['status'] ?? '');

$tipos_permitidos = [
    'Temperatura',
    'Movimento',
    'Umidade',
    'Vibração',
    'Proximidade'
];

if (
    !$id ||
    $nome === '' ||
    $localizacao === '' ||
    $endereco === '' ||
    !in_array($tipo_sensor, $tipos_permitidos, true) ||
    !in_array($status, ['Ativo', 'Inativo'], true) ||
    mb_strlen($nome) > 100 ||
    mb_strlen($localizacao) > 150 ||
    mb_strlen($endereco) > 200
) {
    header('Location: ../index.php?erro_sensor=dados_invalidos');
    exit;
}

$sql = "UPDATE sensores
        SET nome = ?,
            localizacao = ?,
            tipo_sensor = ?,
            endereco = ?,
            status = ?
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "sssssi",
    $nome,
    $localizacao,
    $tipo_sensor,
    $endereco,
    $status,
    $id
);

if ($stmt->execute()) {
    $stmt->close();

    header('Location: ../index.php?sensor_atualizado=sucesso');
    exit;
}

$stmt->close();

header('Location: ../index.php?erro_sensor=atualizar');
exit;
?>
