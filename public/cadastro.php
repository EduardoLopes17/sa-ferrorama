<?php
require_once __DIR__ . '/../infra/verificar_admin.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Cadastro - Ferrovia União</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/style/style.css">
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</head>
<body>
    <div class="cadastro-usuario">
        <div class="container">
        <form class="row g-3" method="POST" action="../backend/cadastrar_usuario.php">

            <h2>Cadastro de usuario</h2>
            <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === '1'): ?>
    <div class="alert alert-success" role="alert">
        Usuário cadastrado com sucesso!
    </div>
<?php endif; ?>

<?php if (isset($_GET['erro'])): ?>
    <div class="alert alert-danger" role="alert">
        <?php
        if ($_GET['erro'] === 'senhas') {
            echo 'As senhas informadas não coincidem.';
        } else {
            echo 'Não foi possível cadastrar o usuário. Verifique os dados e tente novamente.';
        }
        ?>
    </div>
<?php endif; ?>

            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" name="nome" required>
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">CPF</label>
                <input type="text" class="form-control" name="cpf" required>
            </div>

            <div class="col-md-4">
                <label for="inputZip" class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="col-md-6">
                <label for="inputCity" class="form-label">Senha</label>
                <input type="password" class="form-control" name="senha" required>
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Confirmar Senha</label>
                <input type="password" class="form-control" name="confirmar_senha" required>
            </div>

                <div class="col-md-6">
                    <label class="form-label">Perfil</label>
                        <select class="form-control" name="perfil">
                        <option value="usuario">Usuário</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                        <select class="form-control" name="status">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                </div>

                <div class="d-grid mt-3">
                        <button class="btn btn-primary" type="submit">
                            Cadastrar usuário
                        </button>

                <a href="../index.php?aba=gerenciarUsuario"
                    class="btn btn-outline-secondary mt-2">
                    Voltar para Gerenciar Usuários
                </a>
            </div>

            </div>
        </form>
    </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                const parametros = new URLSearchParams(window.location.search);

                if (parametros.get('aba') === 'gerenciarUsuario') {
                mostrarConteudo('gerenciarUsuario');
            }
        });
</script>
</body>

</html>