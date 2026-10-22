<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $perfil = $_POST['perfil'];
    $status = $_POST['status'];
    if ($senha !== $confirmar_senha) {
    header("Location: ../public/cadastro.php?erro=senhas");
    exit;
    }
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);


    $sql = "INSERT INTO usuarios 
    (nome, cpf, email, senha, perfil, status)
    VALUES (?, ?, ?, ?, ?, ?)";


    $stmt = $conexao->prepare($sql);


    $stmt->bind_param(
        "ssssss",
        $nome,
        $cpf,
        $email,
        $senha_hash,
        $perfil,
        $status
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