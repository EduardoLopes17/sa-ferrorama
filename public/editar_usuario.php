<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "SELECT * FROM usuarios WHERE id = ?";

    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $resultado = $stmt->get_result();

    $usuario = $resultado->fetch_assoc();
    if (!$usuario) {
    http_response_code(404);
    exit("Usuário não encontrado.");
}

} else {

    echo "Usuário não informado.";
    exit;

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Ferrovia HELG</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body>

    <div class="cadastro-usuario">
        <div class="container">

            <form class="row g-3" action="../backend/atualizar_usuario.php" method="POST">

                <h2>Editar Usuário</h2>

                <input type="hidden" name="id" value="<?= (int) $usuario['id'] ?>">

                <?php if (isset($_GET['erro'])): ?>
                    <div class="alert alert-danger">
                        Não foi possível atualizar o usuário. Verifique os dados e tente novamente.
                    </div>
                <?php endif; ?>

                <div class="col-md-6">
                    <label class="form-label">Nome Completo</label>
                    <input
                        type="text"
                        class="form-control"
                        name="nome"
                        value="<?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">CPF</label>
                    <input
                        type="text"
                        class="form-control"
                        name="cpf"
                        value="<?= htmlspecialchars($usuario['cpf'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">E-mail</label>
                    <input
                        type="email"
                        class="form-control"
                        name="email"
                        value="<?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Perfil</label>
                    <select class="form-control" name="perfil" required>
                        <option value="usuario" <?= $usuario['perfil'] === 'usuario' ? 'selected' : '' ?>>
                            Usuário
                        </option>
                        <option value="admin" <?= $usuario['perfil'] === 'admin' ? 'selected' : '' ?>>
                            Administrador
                        </option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status" required>
                        <option value="ativo" <?= $usuario['status'] === 'ativo' ? 'selected' : '' ?>>
                            Ativo
                        </option>
                        <option value="inativo" <?= $usuario['status'] === 'inativo' ? 'selected' : '' ?>>
                            Inativo
                        </option>
                    </select>
                </div>

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary">
                        Salvar Alterações
                    </button>

                    <a href="../index.php?aba=gerenciarUsuario"
                       class="btn btn-outline-secondary mt-2">
                        Voltar para Gerenciar Usuários
                    </a>
                </div>

            </form>

        </div>
    </div>

</body>
</html>