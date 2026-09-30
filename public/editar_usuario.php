<?php

require_once "../infra/conexao.php";

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "SELECT * FROM usuarios WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $resultado = $stmt->get_result();

    $usuario = $resultado->fetch_assoc();

} else {

    echo "Usuário não informado.";
    exit;

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

    <div class="container mt-5">

    <h2>Editar Usuário</h2>


    <form action="../backend/atualizar_usuario.php" method="POST">

    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">


    <div class="mb-3">
        <label class="form-label">
            Nome:
        </label>

        <input 
            type="text" 
            name="nome" 
            class="form-control"
            value="<?= $usuario['nome'] ?>">
    </div>


    <div class="mb-3">
        <label class="form-label">
            Email:
        </label>

        <input 
            type="email" 
            name="email"
            class="form-control"
            value="<?= $usuario['email'] ?>">
    </div>


    <button type="submit" class="btn btn-primary">
        Salvar Alterações
    </button>


</form>
    </div>
</body>
</html>