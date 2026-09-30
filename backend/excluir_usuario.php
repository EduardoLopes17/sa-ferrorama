<?php

require_once "../infra/conexao.php";

if (isset($_GET['id'])) {

    $id = $_GET['id'];

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