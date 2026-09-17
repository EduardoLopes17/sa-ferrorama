<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SA Ferrorama</title>
    <link rel="stylesheet" href="./assets/style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

</head>

<body id="P1">
    <header>
        <nav>

            <img class="icon" src="./assets/img/icone trem.png" alt="Ícone Ferrorama">
            <h1>Ferrovia União HELG </h1>
            <img class="icon" src="./assets/img/icone trem.png" alt="Ícone Ferrorama">

        </nav>
    </header>

    <nav id="botoes" class="main-nav">
        <button class="nav-item active" onclick="mostrarConteudo('home')">Home</button>
        <button class="nav-item" onclick="mostrarConteudo('dashboard')">Dashboard</button>
        <button class="nav-item" onclick="mostrarConteudo('monitora')">Monitorar trens</button>
        <button class="nav-item" onclick="mostrarConteudo('alertas')">Alertas Falhas</button>
        <button class="nav-item" onclick="mostrarConteudo('relatorios')">Relatórios</button>
        <button class="nav-item" onclick="mostrarConteudo('cadastro')">Cadastrar Sensores</button>
        <button class="nav-item" onclick="mostrarConteudo('gerenciar')">Gerenciar Sensores</button>
        <button class="nav-item" onclick="mostrarConteudo('cadastro2')">Cadastro</button>
        <button class="nav-item" onclick="mostrarConteudo('gerenciarUsuario')">Gerenciar Usuário</button>
        <button class="nav-item logout" onclick="mostrarConteudo('sair')">Sair</button>
    </nav>


    <div id="home" class="content active">
        <br>

        <h2 class="H222">BEM VINDO A FERROVIA UNIÃO HELG</h2>
        <p class="H222">A melhor plataforma de monitoramento do MUNDO</p>

        <div id="carouselExampleIndicators" class="carousel slide">
            <div class="carousel-indicators">

                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>

                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>

                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="./assets/img/tre-bala cidade. Home.jpeg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="./assets/img/trem- bala moderno. Home.jpg" class="d-block w-100" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="./assets/img/trem- bala tunel. Home.jpg" class="d-block w-100" alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <br>
        <br>
        <center>
            <h2 class="H222"> ROTAS E TRAJETO </h2>

            <div>
                <center><img src="assets/img/trajeto trem.jpg" alt="">
            </div>
    </div>


    <div id="dashboard" class="content">
        <br>
        <br>
        <center><h2>DASHBOARD</h2>
            <br>
        <div class="container">

            <div class="alert-bar">
                <span class="alert-title">2 alertas ativos!</span>
                <span class="alert-text">Sensor 001 offline, Trem TR-45 com atraso</span>
            </div>

            <div class="metrics-grid">
                <div class="card">
                    <div class="card-title">Trens ativos:</div>
                    <div class="card-value text-red">8</div>
                </div>
                <div class="card">
                    <div class="card-title">Sensores online</div>
                    <div class="card-value text-red">47</div>
                </div>
                <div class="card">
                    <div class="card-title">Taxa Sucesso</div>
                    <div class="card-value text-red">98,5%</div>
                </div>
                <div class="card">
                    <div class="card-title">Trocar isso</div>
                    <div class="card-value text-red">42min</div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-title">Trens em operação</div>
                <table>
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-rota">Rota</th>
                            <th class="col-status">Status</th>
                            <th class="col-vel">Velocidade</th>
                            <th class="col-parada">Próxima parada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-id">TR-2401</td>
                            <td class="col-rota">São Paulo &rarr; Rio</td>
                            <td class="col-status status-ok">Em operação</td>
                            <td class="col-vel">120 km/h</td>
                            <td class="col-parada">Santos <span class="text-light">- 15min</span></td>
                        </tr>
                        <tr>
                            <td class="col-id">TR-2402</td>
                            <td class="col-rota">Campinas &rarr; SP</td>
                            <td class="col-status status-ok">Em operação</td>
                            <td class="col-vel">95 km/h</td>
                            <td class="col-parada">Jundiaí <span class="text-light">- 28min</span></td>
                        </tr>
                        <tr>
                            <td class="col-id">TR-2403</td>
                            <td class="col-rota">Campinas &rarr; SP</td>
                            <td class="col-status status-delay">Atraso</td>
                            <td class="col-vel">85 km/h</td>
                            <td class="col-parada">Itu <span class="text-light">- 35min</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    

    <div id="monitora" class="content monitor-page">

    <h2 class="monitor-title">Monitor de Trens</h2>

    <div class="monitor-grid">

        <!-- Trem em operação -->
        <div class="monitor-card">
            <div class="card-label">Trem em operação</div>

            <div class="card-main">
                TR-2401
            </div>

            <div class="card-info">
                São Paulo → Rio de Janeiro
            </div>
        </div>

        <!-- Velocidade -->
        <div class="monitor-card">
            <div class="card-label">Velocidade Atual</div>

            <div class="card-main">
                120 km/h
            </div>

            <div class="card-info">
                Velocidade média: 115 km/h
            </div>
        </div>

        <!-- Status -->
        <div class="monitor-card">
            <div class="card-label">Velocidade Atual</div>

            <div class="card-main status-operacao">
                Em Operação
            </div>

            <div class="card-info">
                Sem atrasos
            </div>
        </div>

        <!-- Próxima parada -->
        <div class="monitor-card">
            <div class="card-label">Próxima parada</div>

            <div class="card-main">
                Santos
            </div>

            <div class="card-info">
                Tempo estimado: 15 min
            </div>
        </div>

    </div>

</div>
    
    
    <div id="relatorios" class="content">
        <h2>Relatórios</h2>

    
    <div class="container">
        <form class="row g-3">

            <div class="col-md-4">
                <label for="inputPassword4" class="form-label">Tipo de Relatório</label>
                <input type="text" class="form-control" id="inputPassword4" placeholder="ex: Desempenho de Trens">
            </div>
            <div class="col-md-4">
                <label for="inputPassword4" class="form-label">Período</label>
                <input type="number" class="form-control" id="inputPassword4" placeholder="ex: Últimos 30 dias">
            </div>

            <div class="col-md-4">
                <label for="inputPassword4" class="form-label">Formato</label>
                <input type="text" class="form-control" id="inputPassword4" placeholder="ex: PDF">
            </div>

            <div class="col-md-12">
                <button class="btn btn-primary" type="button">Gerar Relatório</button>
            </div>
            </form>
        </div>
    </div>


    <div id="cadastro" class="content">
        <h2>Cadastrar Sensores</h2>
        <br>

        <div class="container">
            <form class="row g-3">

                <div class="col-md-6">
                    <label for="inputEmail4" class="form-label">ID do Sensor</label>
                    <input type="text" class="form-control" id="inputEmail4" placeholder="ex: Sensor 01">
                </div>
                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Localização da Plataforma</label>
                    <input type="text" class="form-control" id="inputPassword4" placeholder="ex: Plataforma 1">
                </div>

                <div class="col-md-12">
                    <label for="inputPassword4" class="form-label">Tipo de Sensor</label>
                    <input type="text" class="form-control" id="inputPassword4" placeholder="ex: Temperatura">
                </div>

                <div class="d-grid mt-3">
                    <button class="btn btn-primary" type="button">Cadastrar Sensor</button>
                </div>
            </form>
        </div>
    </div>


    <div id="gerenciar" class="content">

        <div class="col-md-12">

        <h2>Gerenciar Sensores</h2>
                <button class="btn btn-primary" type="button">Novo Sensor</button>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Sensor</th>
                    <th scope="col">Localização</th>
                    <th scope="col">Tipo de Sensor</th>
                    <th scope="col">Endereço</th>
                    <th scope="col">Status do Sensor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Sensor 01</td>
                    <td>Plataforma 1</td>
                    <td>Temperatura</td>
                    <td>Rua Urussanga</td>
                    <td>Ativo</td>

                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>Sensor 02</td>
                    <td>Plataforma 2</td>
                    <td>Movimento</td>
                    <td>Rua Doutor João Colin</td>
                    <td>Ativo</td>

                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td>Sensor 03</td>
                    <td>Plataforma 3</td>
                    <td>Umidade</td>
                    <td>Rua Max Colin</td>
                    <td>Inativo</td>

                </tr>
            </tbody>
        </table>
    </div>


    <div id="cadastro2" class="content">

        <div class="col-md-12">
        
        <h2>Cadastro de Usuario</h2>
                <button class="btn btn-primary" type="button">Novo Usuário</button>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Usuário</th>
                    <th scope="col">Cpf</th>
                    <th scope="col">Email</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Julio</td>
                    <td>182.986.976-18</td>
                    <td>julioferraz@gmail</td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>Bruno</td>
                    <td>145.347.869-09</td>
                    <td>brunolion@gmail</td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td>Alisson</td>
                    <td>172.987.735-78</td>
                    <td>alissongfernandez@gmail</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="gerenciarUsuario" class="content">

        <div class="col-md-12">

        <h2>Gerenciar Usuário</h2>
                <button class="btn btn-primary" type="button">Novo Usuário</button>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Usuário</th>
                    <th scope="col">Cpf</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>Julio</td>
                    <td>182.986.976-18</td>
                    <td>julioferraz@gmail</td>
                    <td>Ativo</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" type="button">Editar</button>
                        <button class="btn btn-sm btn-outline-danger" type="button">Excluir</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>Bruno</td>
                    <td>145.347.869-09</td>
                    <td>brunolion@gmail</td>
                    <td>Ativo</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" type="button">Editar</button>
                        <button class="btn btn-sm btn-outline-danger" type="button">Excluir</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td>Alisson</td>
                    <td>172.987.735-78</td>
                    <td>alissongfernandez@gmail</td>
                    <td>Inativo</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" type="button">Editar</button>
                        <button class="btn btn-sm btn-outline-danger" type="button">Excluir</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <div id="sair" class="content">

        <h2>Sair do Sistema?</h2>
        <p>Sua sessão será encerrada e você será redirecionado para a tela de login!</p>

        <div class="botoes-container">
            <a class="nav-link active" href="./public/login.php">Confirmar</a>
        </div>
    </div>


    <script src="./script/botoes.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>
</html>