<?php

require_once "../infra/conexao.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $perfil = $_POST['perfil'];
    $status = $_POST['status'];

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

    header("Location: ../index.php");

    exit;

} else {
        echo "Erro ao cadastrar usuário: " . $conexao->error;

    }

}

?>