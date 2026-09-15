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
                <h1 id="h13">REDEFINIR SENHA</h1>
                
                <form>
                    <div class="mb-3">
                        <label for="Email1" class="form-label">Nova senha</label>
                        <input type="email" class="form-control custom-input" id="Email1" placeholder="********">
                    </div>
                    
                    <div class="mb-3">
                        <label for="Senha1" class="form-label">Confirmar senha</label>
                        <input type="password" class="form-control custom-input" id="Senha1" placeholder="********">
                    </div>
                    
                    <button type="submit" class="btn-entrar">Redefinir</button>
                    
                    <p class="mt-3 text-center">
                    <a href="login.html" class="signup-link">Redefiniu a senha? faça login!</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

</body>
</html>