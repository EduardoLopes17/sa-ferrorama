<?php

session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: public/login.php');
    exit;
}

require_once "infra/conexao.php";

$sql_usuarios = "SELECT id, nome, cpf, email, status FROM usuarios";

$resultado_usuarios = $conexao->query($sql_usuarios);

$sql_sensores = "SELECT id, nome, localizacao, tipo_sensor, endereco, status FROM sensores ORDER BY id ASC";

$resultado_sensores = $conexao->query($sql_sensores);
$sql_trens = "SELECT
            trens.id,
            trens.codigo,
            trens.modelo,
            trens.velocidade,
            trens.status,
            usuarios.nome AS responsavel
            FROM trens
            LEFT JOIN usuarios
            ON trens.usuario_responsavel_id = usuarios.id
            ORDER BY trens.id ASC";
$resultado_trens = $conexao->query($sql_trens);



$sql_rotas = "SELECT
    rotas.id,
    rotas.nome,
    rotas.status,
    COUNT(paradas.id) AS total_paradas,
    MAX(CASE WHEN paradas.ordem = 1 THEN paradas.nome END) AS ponto_saida,
    MAX(CASE WHEN paradas.ordem = (
        SELECT MAX(p2.ordem)
        FROM paradas p2
        WHERE p2.rota_id = rotas.id
    ) THEN paradas.nome END) AS ponto_chegada
    FROM rotas
    LEFT JOIN paradas ON paradas.rota_id = rotas.id
    GROUP BY rotas.id, rotas.nome, rotas.status
    ORDER BY rotas.id ASC";

$resultado_rotas = $conexao->query($sql_rotas);

?>



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
        <button class="nav-item" onclick="mostrarConteudo('alertas')">Alertas Falhas</button>
        <button class="nav-item" onclick="mostrarConteudo('relatorios')">Relatórios</button>
        <button class="nav-item" onclick="mostrarConteudo('monitora')">Monitorar trens</button>
        <button class="nav-item" onclick="mostrarConteudo('gerenciarTrens')">Gerenciar Trens</button>
        <button class="nav-item" onclick="mostrarConteudo('gerenciarRotas')">Gerenciar Rotas</button>
        <button class="nav-item" onclick="mostrarConteudo('gerenciar')">Gerenciar Sensores</button>

        <?php if ($_SESSION['perfil'] == 'admin') { ?> 

<button class="nav-item" onclick="mostrarConteudo('gerenciarUsuario')">
    Gerenciar Usuário
</button>

<?php } ?>

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
    <br>
    <center><h2 class="monitor-title">Monitor de Trens</h2>

    <br>
    <br>
    <div class="monitor-grid">

        <div class="monitor-card">
            <div class="card-label">Trem em operação</div>

            <div class="card-main">
                TR-2401
            </div>

            <div class="card-info">
                São Paulo → Rio de Janeiro
            </div>
        </div>

        
        <div class="monitor-card">
            <div class="card-label">Velocidade Atual</div>

            <div class="card-main">
                120 km/h
            </div>

            <div class="card-info">
                Velocidade média: 115 km/h
            </div>
        </div>

        <div class="monitor-card">
            <div class="card-label">Velocidade Atual</div>

            <div class="card-main status-operacao">
                Em Operação
            </div>

            <div class="card-info">
                Sem atrasos
            </div>
        </div>

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




    <div id="alertas" class="content">
        <br>
        <br>
        <center><h2>Monitorar Alertas e Falhas</h2>


<br>
    <div class="alerts-card">
        <table class="alerts-table">
            <thead>
                <tr>
                    <th>DATA/HORA</th>
                    <th>TIPO</th>
                    <th>DESCRIÇÃO</th>
                    <th>SEVERIDADE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>18/05/26 14:35:22</td>
                    <td>Sensor</td>
                    <td>Sensor 001 offline</td>
                    <td class="severidade alta">Alta</td>
                </tr>
                <tr>
                    <td>14:22:15</td>
                    <td>Trem</td>
                    <td>Trem TR-45 com atraso de 12 min</td>
                    <td class="severidade media">Média</td>
                </tr>
                <tr>
                    <td>14:10:08</td>
                    <td>Sistema</td>
                    <td>Manutenção programada realizada</td>
                    <td class="severidade baixa">Baixa</td>
                </tr>
                <tr>
                    <td>13:45:30</td>
                    <td>Sensor</td>
                    <td>Sensor 005 voltou online</td>
                    <td class="severidade baixa">Baixa</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
    
    
    <div id="relatorios" class="content">
        <br>
        <br>
        <center><h2>Relatórios</h2></center>

    <br>
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

       
<div id="gerenciarRotas" class="content">

    <div class="cabecalho-gerenciar-rotas">
        <h2>Gerenciar Rotas</h2>

        <?php if (($_SESSION['perfil'] ?? '') === 'admin') { ?>
            <a href="public/cadastro_rotas.php" class="btn-nova-rota">
                Nova Rota
            </a>
        <?php } ?>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nome da Rota</th>
                <th>Ponto de Saída</th>
                <th>Ponto de Chegada</th>
                <th scope="col">Paradas</th>
                <th scope="col">Status</th>
                <th scope="col">Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($resultado_rotas && $resultado_rotas->num_rows > 0) { ?>

                <?php while ($rota = $resultado_rotas->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($rota['id']) ?></td>

                        <td><?= htmlspecialchars($rota['nome']) ?></td>

                        <td><?= htmlspecialchars($rota['ponto_saida'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($rota['ponto_chegada'] ?? '-') ?></td>

                        <td><?= htmlspecialchars($rota['total_paradas']) ?></td>

                        <td><?= htmlspecialchars($rota['status']) ?></td>

                        <td>
                            <?php if (($_SESSION['perfil'] ?? '') === 'admin') { ?>
                                <div class="acoes-rotas">
                                    <a
                                        href="public/editar_rota.php?id=<?= urlencode($rota['id']) ?>"
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        Editar
                                    </a>

                                    <form
                                        action="backend/excluir_rota.php"
                                        method="POST"
                                        onsubmit="return confirm('Deseja realmente excluir esta rota e suas paradas?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= htmlspecialchars($rota['id']) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-outline-danger btn-sm"
                                        >
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            <?php } else { ?>
                                <span>Somente consulta</span>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="7" class="text-center">                        Nenhuma rota cadastrada.
                    </td>
                </tr>

            <?php } ?>
        </tbody>
    </table>

</div>

    
    <div id="gerenciarTrens" class="content">
    
    <br><br>

    <div class="cabecalho-gerenciar-trens">

        <h2>Gerenciar Trens</h2>

        <a href="public/cadastro_trens.php"
           class="btn-novo-trem">
            Novo Trem
        </a>

    </div>

    <br>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Código</th>
                <th scope="col">Modelo</th>
                <th scope="col">Velocidade</th>
                <th scope="col">Status</th>
                <th scope="col">Responsável</th>
                <th scope="col">Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php if ($resultado_trens && $resultado_trens->num_rows > 0) { ?>

                <?php while ($trem = $resultado_trens->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($trem['id']) ?></td>

                        <td><?= htmlspecialchars($trem['codigo']) ?></td>

                        <td><?= htmlspecialchars($trem['modelo']) ?></td>

                        <td>
                            <?= htmlspecialchars($trem['velocidade']) ?> km/h
                        </td>

                        <td><?= htmlspecialchars($trem['status']) ?></td>

                        <td>
                            <?= htmlspecialchars($trem['responsavel'] ?? 'Não definido') ?>
                        </td>

                        <td>
                            <div class="acoes-trens">

                                <a href="public/editar_trem.php?id=<?= urlencode($trem['id']) ?>"
                                   class="btn btn-outline-primary btn-sm">
                                    Editar
                                </a>

                                <form action="backend/excluir_trem.php"
                                      method="POST"
                                      onsubmit="return confirm('Deseja realmente excluir este trem?');">

                                    <input type="hidden" name="id"
                                           value="<?= htmlspecialchars($trem['id']) ?>">

                                    <button type="submit"
                                            class="btn btn-outline-danger btn-sm">
                                        Excluir
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                <?php } ?>

            <?php } else { ?>
                <tr>
                    <td colspan="7" class="text-center">
                        Nenhum trem cadastrado.
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>


    <div id="gerenciar" class="content">
    
<div class="cabecalho-gerenciar-sensores">

    <h2>Gerenciar Sensores</h2>

    <a href="public/cadastro_sensores.php"
       class="btn-novo-sensor">
        Novo Sensor
    </a>

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
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            
<tbody>
    <?php if ($resultado_sensores && $resultado_sensores->num_rows > 0) { ?>

        <?php while ($sensor = $resultado_sensores->fetch_assoc()) { ?>
            <tr>
                <th scope="row">
                    <?= htmlspecialchars($sensor['id']) ?>
                </th>

                <td>
                    <?= htmlspecialchars($sensor['nome']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($sensor['localizacao']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($sensor['tipo_sensor']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($sensor['endereco']) ?>
                </td>

                <td>
                    <?= htmlspecialchars($sensor['status']) ?>
                </td>
                
                <td>
        
    <div class="acoes-sensores">

        <a href="public/editar_sensor.php?id=<?= urlencode($sensor['id']) ?>"
           class="btn btn-outline-primary btn-sm">
            Editar
        </a>

        <form action="backend/excluir_sensor.php" method="POST"
              onsubmit="return confirm('Tem certeza que deseja excluir este sensor?');">

            <input type="hidden" name="id"
                   value="<?= htmlspecialchars($sensor['id']) ?>">

            <button type="submit" class="btn btn-outline-danger btn-sm">
                Excluir
            </button>
        </form>

    </div>
</td>

            </tr>
        <?php } ?>

    <?php } else { ?>

        <tr>
            <td colspan="7" class="text-center">
                Nenhum sensor cadastrado.
            </td>
        </tr>

    <?php } ?>
</tbody>

        </table>
    </div>


    <?php if ($_SESSION['perfil'] == 'admin') { ?>

<div id="gerenciarUsuario" class="content">

    <br>
    <br>

    <div class="col-md-12">

        <center>
            <h2>Gerenciar Usuário</h2>
        </center>

        <br>
        <br>

        <a href="public/cadastro.php">
            <button class="btn btn-primary" type="button">
                Novo Usuário
            </button>
        </a>

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
        <?php while ($usuario = $resultado_usuarios->fetch_assoc()) { ?>

            <tr>
                <th scope="row">
                    <?= $usuario['id'] ?>
                </th>

                <td>
                    <?= $usuario['nome'] ?>
                </td>

                <td>
                    <?= $usuario['cpf'] ?>
                </td>

                <td>
                    <?= $usuario['email'] ?>
                </td>

                <td>
                    <?= $usuario['status'] ?>
                </td>

                <td>
                    <a href="public/editar_usuario.php?id=<?= $usuario['id'] ?>"
                       class="btn btn-sm btn-outline-primary">
                        Editar
                    </a>

                    <a href="backend/excluir_usuario.php?id=<?= $usuario['id'] ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Deseja realmente excluir este usuário?')">
                        Excluir
                    </a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php } ?>

</tbody>
        </table>
    </div>

    <div id="sair" class="content">

        <h2>Sair do Sistema?</h2>
        <p>Sua sessão será encerrada e você será redirecionado para a tela de login!</p>

        <div class="botoes-container">
    <a class="btn-sair" href="./public/login.php">Confirmar</a>
    <button class="btn-sair" type="button" onclick="mostrarConteudo('home')">Cancelar</button>
</div>

    </div>

    <script src="./script/botoes.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>
</html>