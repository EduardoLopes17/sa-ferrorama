<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tela de Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css">
</head>
<body id="P2">

    <div class="container-principal">
        <div class="lado-esquerdo">
            <div class="logo-circulo">
                <img src="../assets/img/icone trem.png" alt="Logo Ferrovia">
            </div>
        </div>

        <div class="lado-direito">
            <div class="login-box">
                <h1 id="h13">LOGIN</h1>
                
                <form>
                    <div class="mb-3">
                        <label for="Usuario" class="form-label">Usuário</label>
                        <input type="usuario" class="form-control custom-input" id="usuario" placeholder="seu_usuario">
                    </div>
                    
                    <div class="mb-3">
                        <label for="Senha1" class="form-label">Senha</label>
                        <input type="password" class="form-control custom-input" id="Senha1" placeholder="********">
                    </div>
                    
                    <button type="submit" class="btn-entrar">Entrar</button>
                    
                    <p class="mt-3 text-center">
                    <a href="nova_senha.html" class="signup-link">Esqueceu a senha? clique aqui!</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>