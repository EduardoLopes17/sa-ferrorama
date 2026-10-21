<?php

<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];

    $sql = "UPDATE usuarios 
            SET nome = ?, email = ?
            WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param(
        "ssi",
        $nome,
        $email,
        $id
    );

    if ($stmt->execute()) {

        echo "Usuário atualizado com sucesso!";

        header("Refresh:2; url=../index.php");

    } else {

        echo "Erro ao atualizar usuário: " . $conexao->error;

    }

}

?>