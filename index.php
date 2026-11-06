<?php

session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (empty($_SESSION['usuario_id'])) {
    header('Location: public/conheca_helg.php');
    exit;
}

if (($_GET['erro_trem'] ?? '') === 'rota_ativa') {
    echo "<script>
        alert('Não é possível excluir este trem porque ele possui uma rota ativa. Desative a rota antes de excluir.');
    </script>";
}

if (($_GET['erro_usuario'] ?? '') === 'responsavel_trem') {
    echo "<script>
        alert('Não é possível excluir este usuário porque ele é responsável por um trem. Altere o responsável antes de excluir.');
    </script>";
}

require_once "infra/conexao.php";
 
$sql_dashboard = "SELECT
    (SELECT COUNT(*) FROM trens WHERE status = 'Em Operação') AS trens_ativos,
    (SELECT COUNT(*) FROM sensores WHERE status = 'Ativo') AS sensores_ativos,
    (SELECT COUNT(*) FROM rotas WHERE status = 'Ativa') AS rotas_ativas,
    (SELECT COUNT(*) FROM usuarios WHERE status = 'ativo') AS usuarios_ativos";

$resultado_dashboard = $conexao->query($sql_dashboard);
$dados_dashboard = $resultado_dashboard->fetch_assoc();

$sql_trens_dashboard = "SELECT
    trens.codigo,
    trens.modelo,
    trens.velocidade,
    trens.status,
    rotas.nome AS nome_rota
    FROM trens
    LEFT JOIN rotas ON trens.rota_id = rotas.id
    ORDER BY trens.codigo ASC";

$resultado_trens_dashboard = $conexao->query($sql_trens_dashboard);
$sql_alertas_trens = "SELECT
    codigo,
    modelo,
    status
    FROM trens
    WHERE status IN ('Manutenção', 'Inativo')
    ORDER BY codigo ASC";

$resultado_alertas_trens = $conexao->query($sql_alertas_trens);

$sql_alertas_sensores = "SELECT
    id,
    nome,
    localizacao,
    status,
    criado_em
    FROM sensores
    WHERE status = 'Inativo'
    ORDER BY nome ASC";

$resultado_alertas_sensores = $conexao->query($sql_alertas_sensores);

$sql_total_alertas = "SELECT
    (
        SELECT COUNT(*)
        FROM trens
        WHERE status IN ('Manutenção', 'Inativo')
    ) +
    (
        SELECT COUNT(*)
        FROM sensores
        WHERE status = 'Inativo'
    ) AS total";

$resultado_total_alertas = $conexao->query($sql_total_alertas);
$dados_total_alertas = $resultado_total_alertas->fetch_assoc();

$total_alertas = (int) $dados_total_alertas['total'];

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


$sql_monitoramento = "SELECT
    trens.id,
    trens.codigo,
    trens.modelo,
    trens.velocidade,
    trens.status,
    trens.rota_id,
    rotas.nome AS nome_rota
    FROM trens
    LEFT JOIN rotas ON trens.rota_id = rotas.id
    ORDER BY trens.codigo ASC";

$resultado_monitoramento = $conexao->query($sql_monitoramento);

$trens_monitoramento = [];

while ($trem_monitoramento = $resultado_monitoramento->fetch_assoc()) {
    $trens_monitoramento[] = $trem_monitoramento;
}




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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/style/style.css?v=5">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

</head>

<body id="P1">
    <header>
    <nav class="topo-sistema">
        <div class="marca-sistema">
            <img class="icon" src="./assets/img/icone.trem.png" alt="Ícone Ferrorama">
            <h1>Ferrovia União HELG</h1>
        </div>

        <div class="usuario-topo">
            <span class="perfil-usuario">
                <?= htmlspecialchars(
                    ($_SESSION['perfil'] ?? '') === 'admin' ? 'Administrador' : 'Usuário',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>

            <a class="btn-sair-topo" href="./backend/logout.php" onclick="return confirm('Tem certeza de que deseja sair do sistema?')">
                Sair
            </a>
        </div>
    </nav>
</header>

    
            <nav id="botoes" class="main-nav">
                <button class="nav-item active" onclick="mostrarConteudo('home')">Home</button>
                <button class="nav-item" onclick="mostrarConteudo('dashboard')">Dashboard</button>
                <button class="nav-item" onclick="mostrarConteudo('alertas')">Alertas Falhas</button>
                <button class="nav-item" onclick="mostrarConteudo('relatorios')">Relatórios</button>
                <button class="nav-item" onclick="mostrarConteudo('monitora')">Monitorar trens</button>

            <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                <button class="nav-item" onclick="mostrarConteudo('gerenciarTrens')"> Trens</button>
                <button class="nav-item" onclick="mostrarConteudo('gerenciarRotas')"> Rotas</button>
                <button class="nav-item" onclick="mostrarConteudo('gerenciar')"> Sensores</button>
                <button class="nav-item" onclick="mostrarConteudo('gerenciarUsuario')"> Usuário</button>
            <?php endif; ?>
            </nav>
            

    <div id="home" class="content active">
        <br>

        
<div class="boas-vindas-home">
    <h2>
        Bem-vindo(a) à Ferrovia HELG,
        <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário', ENT_QUOTES, 'UTF-8') ?>!
    </h2>

    <p>
        É um prazer ter você aqui! Acompanhe e gerencie as operações ferroviárias em nossa plataforma.
    </p>
</div>

                <section class="clima-painel" aria-label="Informações meteorológicas">
        <div class="clima-cabecalho">
            <div>
                <h3>Condições meteorológicas</h3>
                <p id="clima-localizacao">Joinville, SC</p>
            </div>
                <span id="clima-temperatura">-- °C</span>
        </div>

                <p id="clima-condicao" aria-live="polite">Consultando clima...</p>
                <p id="clima-atualizacao">Aguardando atualização</p>
            </section>


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
            </div>

    <div id="dashboard" class="content">
        
        <center><h2>DASHBOARD</h2>

        <div class="container">

            <div class="alert-bar">
    <span class="alert-title">
        <?= $total_alertas ?>
        <?= $total_alertas === 1 ? 'alerta ativo!' : 'alertas ativos!' ?>
    </span>

    <span class="alert-text">
        <?php if ($total_alertas > 0): ?>
            Existem <?= $total_alertas ?> ocorrências que precisam de atenção.
            Consulte a aba Alertas e Falhas.
        <?php else: ?>
            Nenhuma ocorrência identificada.
        <?php endif; ?>
    </span>
</div>

            <div class="metrics-grid">
                <div class="card">
    <div class="card-title">Trens em operação</div>
    <div class="card-value text-red">
        <?= (int) $dados_dashboard['trens_ativos'] ?>
    </div>
</div>

<div class="card">
    <div class="card-title">Sensores ativos</div>
    <div class="card-value text-red">
        <?= (int) $dados_dashboard['sensores_ativos'] ?>
    </div>
</div>

<div class="card">
    <div class="card-title">Rotas ativas</div>
    <div class="card-value text-red">
        <?= (int) $dados_dashboard['rotas_ativas'] ?>
    </div>
</div>

<div class="card">
    <div class="card-title">Usuários ativos</div>
    <div class="card-value text-red">
        <?= (int) $dados_dashboard['usuarios_ativos'] ?>
    </div>
</div>
</div>
<div class="dashboard-tabela">
    <h3>Trens cadastrados</h3>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Modelo</th>
                    <th>Rota</th>
                    <th>Velocidade</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($resultado_trens_dashboard->num_rows > 0): ?>
                    <?php while ($trem = $resultado_trens_dashboard->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($trem['codigo']) ?></td>
                            <td><?= htmlspecialchars($trem['modelo']) ?></td>
                            <td><?= htmlspecialchars($trem['nome_rota'] ?? 'Sem rota') ?></td>
                            <td><?= (float) $trem['velocidade'] ?> km/h</td>
                            <td><?= htmlspecialchars($trem['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            Nenhum trem cadastrado.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
    

   <div id="monitora" class="content monitor-page">
    

    <h2 class="monitor-title">Monitor de Trens</h2>

    
<div class="selecao-trem-monitoramento">

    <label for="selecionar-trem">Selecionar Trem</label>

    <select id="selecionar-trem" class="form-select">

        <option value="">Selecione um trem</option>

        <?php foreach ($trens_monitoramento as $trem_monitoramento) { ?>

            <option value="<?= htmlspecialchars($trem_monitoramento['id']) ?>">
                <?= htmlspecialchars($trem_monitoramento['codigo']) ?>
                -
                <?= htmlspecialchars($trem_monitoramento['modelo']) ?>
            </option>

        <?php } ?>

    </select>

    <?php if (empty($trens_monitoramento)) { ?>
        <p>Nenhum trem cadastrado para monitoramento.</p>
    <?php } ?>

</div>

    <br>
    <br>
    
    <div class="monitor-grid">

    <div class="monitor-card">
        <div class="card-label">Trem selecionado</div>
        <div class="card-main" id="monitor-codigo">--</div>
        <div class="card-info" id="monitor-modelo">Selecione um trem</div>
    </div>

    <div class="monitor-card">
        <div class="card-label">Velocidade Atual</div>
        <div class="card-main" id="monitor-velocidade">-- km/h</div>
        <div class="card-info">Velocidade cadastrada no sistema</div>
    </div>

    <div class="monitor-card">
        <div class="card-label">Status</div>
        <div class="card-main" id="monitor-status">--</div>
        <div class="card-info">Situação atual do trem</div>
    </div>

    <div class="monitor-card">
        <div class="card-label">Rota do Trem</div>
        <div class="card-main" id="monitor-rota">--</div>
        <div class="card-info">Rota vinculada ao trem</div>
    </div>

    </div>
            <div class="monitor-mapa-container">
                <h3>Mapa de Monitoramento</h3>
            <div id="mapa-trens"></div>
            </div>
    </div>



    <div id="alertas" class="content">
    <h2 class="titulo-alertas">Monitorar Alertas e Falhas</h2>


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
    <?php while ($alerta = $resultado_alertas_trens->fetch_assoc()): ?>
        <?php
            $manutencao = $alerta['status'] === 'Manutenção';
            $severidade = $manutencao ? 'Alta' : 'Média';
            $classe = $manutencao ? 'alta' : 'media';
        ?>
        <tr>
            <td>—</td>
            <td>Trem</td>
            <td>
                Trem <?= htmlspecialchars($alerta['codigo']) ?>
                está <?= $manutencao ? 'em manutenção' : 'inativo' ?>.
            </td>
            <td class="severidade <?= $classe ?>">
                <?= $severidade ?>
            </td>
        </tr>
    <?php endwhile; ?>

    <?php while ($sensor = $resultado_alertas_sensores->fetch_assoc()): ?>
        <tr>
            <td>—</td>
            <td>Sensor</td>
            <td>
                Sensor <?= htmlspecialchars($sensor['nome']) ?>
                está inativo.
                Localização: <?= htmlspecialchars($sensor['localizacao']) ?>.
            </td>
            <td class="severidade alta">Alta</td>
        </tr>
    <?php endwhile; ?>

    <?php if (
        $resultado_alertas_trens->num_rows === 0 &&
        $resultado_alertas_sensores->num_rows === 0
    ): ?>
        <tr>
            <td colspan="4" class="text-center">
                Nenhum alerta encontrado.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
    </div>

</div>
    
    
    <div id="relatorios" class="content">
    <div class="relatorios-container">

        <h2>Relatórios</h2>

        <form id="form-relatorios" class="form-relatorios" method="GET" action="./public/gerar_relatorio.php">
            <div class="relatorios-campos">

                <div class="relatorios-campo">
                    <label for="tipo-relatorio">Tipo de Relatório</label>
                    <select id="tipo-relatorio" name="tipo" required>
                        <option value="">Selecione um relatório</option>
                        <option value="trens">Relatório de Trens</option>
                        <option value="rotas">Relatório de Rotas</option>
                        <option value="sensores">Relatório de Sensores</option>
                        <option value="usuarios">Relatório de Usuários</option>
                        <option value="alertas">Relatório de Alertas e Falhas</option>
                    </select>
                </div>

                <div class="relatorios-campo">
                    <label for="periodo-relatorio">Período</label>
                    <select id="periodo-relatorio" name="periodo" required>
                        <option value="todos">Todos os registros</option>
                        <option value="7">Últimos 7 dias</option>
                        <option value="30">Últimos 30 dias</option>
                    </select>
                </div>

                <div class="relatorios-campo">
                    <label for="formato-relatorio">Formato</label>
                    <select id="formato-relatorio" name="formato" required>
                        <option value="pdf">PDF</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>

            </div>

            <div class="relatorios-acoes">
                    <button type="submit" class="btn-gerar-relatorio">                    Gerar Relatório
                </button>
            </div>

        </form>
    </div>
</div>

       
<div id="gerenciarRotas" class="content">

    <div class="cabecalho-gerenciar-rotas cabecalho-gerenciar-usuario">
        <h2>Gerenciar Rotas</h2>

        <?php if (($_SESSION['perfil'] ?? '') === 'admin') { ?>
           <button type="button"
        class="btn-nova-rota"
        data-bs-toggle="modal"
        data-bs-target="#modalRota">
     Cadastrar Rota
</button>
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

<div class="modal fade" id="modalRota" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">Cadastrar Rota</h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <form action="public/cadastro_rotas.php" method="POST">

                    <div class="mb-3">
                        <label for="nomeRota">Nome da Rota</label>

                        <input type="text"
                               id="nomeRota"
                               name="nome"
                               class="form-control"
                               maxlength="100"
                               placeholder="Ex.: Linha Centro - Vila Nova"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="statusRota">Status</label>

                        <select id="statusRota"
                                name="status"
                                class="form-select"
                                required>

                            <option value="Ativa">Ativa</option>
                            <option value="Inativa">Inativa</option>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="tremRota">Trem Responsável</label>

                        <select id="tremRota"
                                name="trem_id"
                                class="form-select"
                                required>

                            <option value="">Selecione um trem</option>

                            <?php
                            $lista_trens = $conexao->query(
                                "SELECT id, codigo, modelo
                                 FROM trens
                                 ORDER BY codigo ASC"
                            );

                            while ($trem = $lista_trens->fetch_assoc()) {
                            ?>

                                <option value="<?= (int)$trem['id'] ?>">
                                    <?= htmlspecialchars($trem['codigo'] . ' - ' . $trem['modelo']) ?>
                                </option>

                            <?php } ?>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="saidaRota">Ponto de Saída</label>

                        <select id="saidaRota"
                                name="saida"
                                class="form-select"
                                required>

                            <option value="">Selecione o ponto de saída</option>

                            <?php
                            $bairros_rota = [
                                'Adhemar Garcia',
                                'América',
                                'Anita Garibaldi',
                                'Atiradores',
                                'Aventureiro',
                                'Boa Vista',
                                'Boehmerwald',
                                'Bucarein',
                                'Centro',
                                'Costa e Silva',
                                'Dona Francisca',
                                'Espinheiros',
                                'Fátima',
                                'Floresta',
                                'Glória',
                                'Guanabara',
                                'Iririú',
                                'Itaum',
                                'Itinga',
                                'Jardim Iririú',
                                'Jardim Paraíso',
                                'Jarivatuba',
                                'João Costa',
                                'Morro do Meio',
                                'Nova Brasília',
                                'Paranaguamirim',
                                'Petrópolis',
                                'Pirabeiraba',
                                'Saguaçu',
                                'Santo Antônio',
                                'Ulysses Guimarães',
                                'Vila Nova'
                            ];

                            foreach ($bairros_rota as $bairro) {
                            ?>

                                <option value="<?= htmlspecialchars($bairro) ?>">
                                    <?= htmlspecialchars($bairro) ?>
                                </option>

                            <?php } ?>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="chegadaRota">Ponto de Chegada</label>

                        <select id="chegadaRota"
                                name="chegada"
                                class="form-select"
                                required>

                            <option value="">Selecione o ponto de chegada</option>

                            <?php foreach ($bairros_rota as $bairro) { ?>

                                <option value="<?= htmlspecialchars($bairro) ?>">
                                    <?= htmlspecialchars($bairro) ?>
                                </option>

                            <?php } ?>

                        </select>
                    </div>

                    <div class="mb-3">

                        <h6>Paradas Intermediárias</h6>

                        <div id="lista-paradas-modal"></div>

                        <button type="button"
                                id="adicionar-parada-modal"
                                class="btn btn-outline-primary btn-sm mt-2">

                            + Adicionar Parada

                        </button>

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button type="submit"
                                class="btn btn-primary">

                            Cadastrar Rota

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

</div>

    
    <div id="gerenciarTrens" class="content">


    
<div class="cabecalho-gerenciar-trens">

    <h2>Gerenciar Trens</h2>

    <button type="button"
        class="btn-novo-trem"
        data-bs-toggle="modal"
        data-bs-target="#modalTrem"
        onclick="novoTrem()">
        Cadastrar Trem
    </button>

</div>


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

<div class="modal fade" id="modalTrem" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="tituloModalTrem">
                    Cadastro de Trem
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <form action="public/cadastro_trens.php"
                 method="POST"
                id="formTrem">

                    <input type="hidden"
                           name="id"
                           id="id_trem">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Código do Trem</label>
                            <input type="text"
                                   name="codigo"
                                   id="codigo"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Modelo do Trem</label>
                            <input type="text"
                                   name="modelo"
                                   id="modelo"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Velocidade (km/h)</label>
                            <input type="number"
                                   name="velocidade"
                                   id="velocidade"
                                   class="form-control"
                                   min="0"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Status</label>

                            <select name="status"
                                    id="status"
                                    class="form-select">

                                <option value="Parado">Parado</option>
                                <option value="Em Operação">Em Operação</option>
                                <option value="Manutenção">Manutenção</option>

                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Funcionário Responsável</label>

                            <select name="usuario_responsavel_id"
                                    id="responsavel_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Selecione um funcionário
                                </option>

                                <?php
                                $funcionarios = $conexao->query(
                                    "SELECT id, nome FROM usuarios
                                     WHERE status = 'ativo'
                                     ORDER BY nome"
                                );

                                while ($funcionario = $funcionarios->fetch_assoc()):
                                ?>

                                <option value="<?= $funcionario['id'] ?>">
                                    <?= htmlspecialchars($funcionario['nome']) ?>
                                </option>

                                <?php endwhile; ?>

                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit"
                                class="btn btn-primary"
                                id="botaoSalvarTrem">
                            Cadastrar Trem
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

</div>


    <div id="gerenciar" class="content">
    
<div class="cabecalho-gerenciar-sensores">

    <h2>Gerenciar Sensores</h2>

    <button type="button"
        class="btn-novo-sensor"
        data-bs-toggle="modal"
        data-bs-target="#modalSensor">
    Novo Sensor
</button>

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


<div class="modal fade" id="modalSensor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Sensor</h5>

                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
            </div>

            <form method="POST" action="public/cadastro_sensores.php">

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="nomeSensor" class="form-label">
                            Nome do Sensor
                        </label>

                        <input type="text"
                               class="form-control"
                               id="nomeSensor"
                               name="nome"
                               placeholder="Ex.: Sensor 01"
                               maxlength="100"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="localizacaoSensor" class="form-label">
                            Localização da Plataforma
                        </label>

                        <input type="text"
                               class="form-control"
                               id="localizacaoSensor"
                               name="localizacao"
                               placeholder="Ex.: Plataforma 1"
                               maxlength="150"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="tipoSensor" class="form-label">
                            Tipo de Sensor
                        </label>

                        <select class="form-select"
                                id="tipoSensor"
                                name="tipo_sensor"
                                required>
                            <option value="">Selecione o tipo</option>
                            <option value="Temperatura">Temperatura</option>
                            <option value="Movimento">Movimento</option>
                            <option value="Umidade">Umidade</option>
                            <option value="Vibração">Vibração</option>
                            <option value="Proximidade">Proximidade</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="enderecoSensor" class="form-label">
                            Endereço
                        </label>

                        <input type="text"
                               class="form-control"
                               id="enderecoSensor"
                               name="endereco"
                               placeholder="Ex.: Rua Urussanga"
                               maxlength="200"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="statusSensor" class="form-label">
                            Status
                        </label>

                        <select class="form-select"
                                id="statusSensor"
                                name="status"
                                required>
                            <option value="Ativo">Ativo</option>
                            <option value="Inativo">Inativo</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Cadastrar Sensor
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

    </div>


    <?php if ($_SESSION['perfil'] == 'admin') { ?>

<div id="gerenciarUsuario" class="content">

            <?php if (($_GET['sucesso'] ?? '') === 'usuario_atualizado'): ?>

    <div id="mensagem-usuario-atualizado" class="alert alert-success text-center" role="alert">
    Usuário atualizado com sucesso!
    </div>
<?php endif; ?>


    <div class="cabecalho-gerenciar-usuario">
    <h2>Gerenciar Usuário</h2>

    <button type="button"
        class="btn-novo-sensor"
        data-bs-toggle="modal"
        data-bs-target="#modalUsuario">
        Novo Usuário
    </button>


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

<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Usuário</h5>

                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
            </div>

            <form method="POST" action="backend/cadastrar_usuario.php">

                <div class="modal-body">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" class="form-control"
                                   name="nome" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">CPF</label>
                            <input type="text" class="form-control"
                                   id="cpf" name="cpf"
                                   maxlength="14"
                                   inputmode="numeric"
                                   placeholder="000.000.000-00"
                                   pattern="[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}"
                                   title="Informe um CPF válido"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">E-mail</label>
                            <input type="email" class="form-control"
                                   name="email" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">CEP</label>
                            <input type="text" class="form-control"
                                   id="cep" name="cep"
                                   maxlength="9"
                                   placeholder="00000-000"
                                   required>
                            <div id="cep-mensagem" class="form-text"></div>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Logradouro</label>
                            <input type="text" class="form-control"
                                   id="logradouro" name="logradouro"
                                   maxlength="150" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Número</label>
                            <input type="text" class="form-control"
                                   id="numero" name="numero"
                                   maxlength="20" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Bairro</label>
                            <input type="text" class="form-control"
                                   id="bairro" name="bairro"
                                   maxlength="100" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Cidade</label>
                            <input type="text" class="form-control"
                                   id="cidade" name="cidade"
                                   maxlength="100" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Estado (UF)</label>
                            <input type="text" class="form-control"
                                   id="uf" name="uf"
                                   maxlength="2" required>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Complemento (opcional)</label>
                            <input type="text" class="form-control"
                                   name="complemento"
                                   maxlength="150">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Senha</label>
                            <input type="password" class="form-control"
                                   name="senha" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirmar Senha</label>
                            <input type="password" class="form-control"
                                   name="confirmar_senha" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Perfil</label>
                            <select class="form-select" name="perfil" required>
                                <option value="usuario">Usuário</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Cadastrar Usuário
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

</div>
<?php } ?>

</tbody>
        </table>
    </div>

        <script id="dados-monitoramento" type="application/json"><?= json_encode(
            $trens_monitoramento,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
        </script>
        
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="./script/monitorar_trens.js"></script>
<script src="./script/clima.js" defer></script>

    <script src="./script/botoes.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
                
        <script>
document.addEventListener('DOMContentLoaded', function () {
    const mensagem = document.getElementById('mensagem-usuario-atualizado');

    if (mensagem) {
        setTimeout(function () {
            mensagem.remove();

            const url = new URL(window.location.href);
            url.searchParams.delete('sucesso');
            window.history.replaceState({}, '', url);
        }, 8000);
    }
});
</script>

 <script src="script/script.js"></script>


</body>

</html>