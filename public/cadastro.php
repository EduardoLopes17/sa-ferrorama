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
                    Cadastrar usuario
                </button>

            </div>
        </form>
    </div>

</body>

</html>