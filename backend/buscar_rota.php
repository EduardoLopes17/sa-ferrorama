
<?php
require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

header('Content-Type: application/json; charset=utf-8');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Rota inválida.']);
    exit;
}

$stmt = $conexao->prepare(
    "SELECT id, nome, status FROM rotas WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$rota = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$rota) {
    http_response_code(404);
    echo json_encode(['erro' => 'Rota não encontrada.']);
    exit;
}

$stmt = $conexao->prepare(
    "SELECT id FROM trens WHERE rota_id = ? LIMIT 1"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$trem = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conexao->prepare(
    "SELECT nome FROM paradas WHERE rota_id = ? ORDER BY ordem ASC"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

$paradas = [];

while ($linha = $resultado->fetch_assoc()) {
    $paradas[] = $linha['nome'];
}

$stmt->close();

echo json_encode([
    'id' => (int) $rota['id'],
    'nome' => $rota['nome'],
    'status' => $rota['status'],
    'trem_id' => (int) ($trem['id'] ?? 0),
    'saida' => $paradas[0] ?? '',
    'chegada' => count($paradas) >= 2 ? end($paradas) : '',
    'intermediarias' => array_slice($paradas, 1, -1)
], JSON_UNESCAPED_UNICODE);
