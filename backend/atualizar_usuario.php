<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?aba=gerenciarUsuario');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nome = trim($_POST['nome'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$email = trim($_POST['email'] ?? '');
$perfil = $_POST['perfil'] ?? '';
$status = $_POST['status'] ?? '';

if (
    !$id ||
    $nome === '' ||
    $cpf === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !in_array($perfil, ['usuario', 'admin'], true) ||
    !in_array($status, ['ativo', 'inativo'], true)
) {
    header('Location: ../public/editar_usuario.php?id=' . (int) $id . '&erro=1');
    exit;
}

$sql = "UPDATE usuarios
        SET nome = ?, cpf = ?, email = ?, perfil = ?, status = ?
        WHERE id = ?";

$stmt = $conexao->prepare($sql);

$stmt->bind_param(
    "sssssi",
    $nome,
    $cpf,
    $email,
    $perfil,
    $status,
    $id
);

try {
    $stmt->execute();

    header('Location: ../index.php?aba=gerenciarUsuario&sucesso=usuario_atualizado');
    exit;

} catch (mysqli_sql_exception $e) {

    header('Location: ../public/editar_usuario.php?id=' . $id . '&erro=1');
    exit;
}