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
    
    <div class="container">
        <form class="row g-3">

            <h2>Cadastro de usuario</h2>
            <p>Preencha os dados abaixo para realizar o registro completo do usuario no sistema acadêmico.</p>
            <div class="col-md-6">
                <label for="inputEmail4" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="inputEmail4">
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">CPF</label>
                <input type="number" class="form-control" id="inputPassword4">
            </div>


            <div class="col-md-4">
                <label for="inputCity" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" id="inputCity">
            </div>
            <div class="col-md-4">
                <label for="inputPassword4" class="form-label">Telefone</label>
                <input type="number" class="form-control" id="inputPassword4">
            </div>
            <div class="col-md-4">
                <label for="inputZip" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="inputZip">
            </div>



            <div class="col-12">
                <label for="inputAddress" class="form-label">Endereço completo</label>
                <input type="text" class="form-control" id="inputAddress" placeholder="1234 Urusanga">
            </div>


            <div class="col-md-4">
                <label for="inputCity" class="form-label">Cidade</label>
                <input type="text" class="form-control" id="inputCity">
            </div>
            <div class="col-md-4">
                <label for="inputPassword4" class="form-label">Estado</label>
                <input type="text" class="form-control" id="inputPassword4">
            </div>
            <div class="col-md-4">
                <label for="inputZip" class="form-label">CEP</label>
                <input type="number" class="form-control" id="inputZip">
            </div>
            <div class="col-md-6">
                <label for="inputCity" class="form-label">Senha</label>
                <input type="text" class="form-control" id="inputCity">
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Confirmar Senha</label>
                <input type="text" class="form-control" id="inputPassword4">
            </div>


            <div class="d-grid mt-3">
                <button class="btn btn-primary" type="button">Cadastrar usuario</button>
            </div>
        </form>
    </div>

</body>

</html>