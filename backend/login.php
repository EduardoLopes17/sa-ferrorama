<?php

session_start();

require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    header('Location: ../public/login.php?erro=preencha');
    exit;
}

$sql = "
    SELECT
        id,
        nome,
        email,
        senha,
        perfil,
        status
    FROM usuarios
    WHERE email = ?
    LIMIT 1
";

$stmt = $conexao->prepare($sql);

$stmt->bind_param('s', $email);

$stmt->execute();

$resultado = $stmt->get_result();

$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    header('Location: ../public/login.php?erro=login');
    exit;
}

if ($usuario['status'] !== 'ativo') {
    header('Location: ../public/login.php?erro=inativo');
    exit;
}

if ($usuario['perfil'] !== 'admin') {
    header('Location: ../public/login.php?erro=naoadmin');
    exit;
}

if (!password_verify($senha, $usuario['senha'])) {
    header('Location: ../public/login.php?erro=login');
    exit;
}

session_regenerate_id(true);

$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['nome'] = $usuario['nome'];
$_SESSION['email'] = $usuario['email'];
$_SESSION['perfil'] = $usuario['perfil'];

header('Location: ../index.php');

exit;