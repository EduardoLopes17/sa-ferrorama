<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    
$cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');

function validarCPF($cpf) {
    if (!preg_match('/^\d{11}$/', $cpf)) {
        return false;
    }

    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($tamanho = 9; $tamanho < 11; $tamanho++) {
        $soma = 0;

        for ($i = 0; $i < $tamanho; $i++) {
            $soma += (int) $cpf[$i] * ($tamanho + 1 - $i);
        }

        $resto = ($soma * 10) % 11;
        $digito = $resto === 10 ? 0 : $resto;

        if ((int) $cpf[$tamanho] !== $digito) {
            return false;
        }
    }

    return true;
}

if (!validarCPF($cpf)) {
    header("Location: ../public/cadastro.php?erro=cpf_invalido");
    exit;
}

$consulta = $conexao->prepare(
    "SELECT id FROM usuarios WHERE REPLACE(REPLACE(cpf, '.', ''), '-', '') = ? LIMIT 1"
);

$consulta->bind_param("s", $cpf);
$consulta->execute();
$consulta->store_result();

if ($consulta->num_rows > 0) {
    $consulta->close();
    header("Location: ../public/cadastro.php?erro=cpf_existente");
    exit;
}

$consulta->close();

    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $perfil = $_POST['perfil'];
    $status = $_POST['status'];
    $cep = trim($_POST['cep'] ?? '');
$logradouro = trim($_POST['logradouro'] ?? '');
$bairro = trim($_POST['bairro'] ?? '');
$cidade = trim($_POST['cidade'] ?? '');
$uf = strtoupper(trim($_POST['uf'] ?? ''));
$numero = trim($_POST['numero'] ?? '');
$complemento = trim($_POST['complemento'] ?? '');

$cep = preg_replace('/\D/', '', $cep);

if (
    !preg_match('/^\d{8}$/', $cep) ||
    $logradouro === '' ||
    $bairro === '' ||
    $cidade === '' ||
    !preg_match('/^[A-Z]{2}$/', $uf) ||
    $numero === ''
) {
    header("Location: ../public/cadastro.php?erro=endereco");
    exit;
}

$cep = substr($cep, 0, 5) . '-' . substr($cep, 5);
    if ($senha !== $confirmar_senha) {
    header("Location: ../public/cadastro.php?erro=senhas");
    exit;
    }
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);


    $sql = "INSERT INTO usuarios
    (nome, cpf, email, senha, perfil, status,
    cep, logradouro, bairro, cidade, uf, numero, complemento)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


    $stmt = $conexao->prepare($sql);


    $stmt->bind_param(
    "sssssssssssss",
    $nome,
    $cpf,
    $email,
    $senha_hash,
    $perfil,
    $status,
    $cep,
    $logradouro,
    $bairro,
    $cidade,
    $uf,
    $numero,
    $complemento
);


    if ($stmt->execute()) {

    header("Location: ../public/cadastro.php?sucesso=1");
    exit;

} else {

    header("Location: ../public/cadastro.php?erro=cadastro");
    exit;

}

}

?>