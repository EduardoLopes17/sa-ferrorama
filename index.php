<?php

session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (empty($_SESSION['usuario_id'])) {
    header('Location: public/login.php');
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
    <link rel="stylesheet" href="./assets/style/style.css?v=3">
    <link rel="stylesheet" href="./assets/style/conheca_helg.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
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
                <button class="nav-item" onclick="mostrarConteudo('conhecaHelg')">Conheça a HELG</button>
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

                <button class="nav-item logout" onclick="mostrarConteudo('sair')">Sair</button>

            </nav>
            

                

<div id="conhecaHelg" class="content">
    <section class="helg-apresentacao">
        <div class="helg-apresentacao-texto">
            <span class="helg-etiqueta">CONHEÇA NOSSO SISTEMA</span>
            <h1>Ferrovia HELG</h1>
            <h2>Gerenciamento e monitoramento ferroviário</h2>
            <p>
                A Ferrovia HELG é uma plataforma desenvolvida para
                auxiliar no gerenciamento de operações ferroviárias,
                reunindo trens, rotas, sensores e alertas em um único sistema.
            </p>
            <p>
                Conheça as funcionalidades disponíveis e descubra
                como utilizar cada uma delas.
            </p>
            <a href="#helg-funcionalidades" class="helg-botao">
                Explorar funcionalidades
            </a>
        </div>

        <div class="helg-apresentacao-imagem">
            <img src="./assets/img/ferrovia. Home.jpg" alt="Ferrovia HELG">
        </div>
    </section>

    <section id="helg-funcionalidades" class="helg-funcionalidades">
        <div class="helg-secao-titulo">
            <span class="helg-etiqueta">GUIA DO SISTEMA</span>
            <h2>Conheça as funcionalidades</h2>
            <p>
                Veja como funciona cada área da plataforma e
                aprenda a utilizar os recursos disponíveis.
            </p>
        </div>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="assets/img/tutorial/home.png" alt="Tela Home" loading="lazy">            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">01 / INÍCIO</span>
                <h3>Home</h3>
                <p>
                    A Home é a página inicial do sistema. Ela apresenta
                    uma saudação ao usuário conectado e um carrossel
                    de imagens relacionadas ao transporte ferroviário.
                </p>
                <h4>Como utilizar?</h4>
                <p>
                    Após realizar o login, acesse a aba Home para
                    visualizar a página inicial da plataforma.
                </p>
                <button type="button" class="helg-botao" onclick="mostrarConteudo('home')">
                    Acessar Home
                </button>
            </div>
        </article>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/dashboard.png" alt="Tela Dashboard" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">02 / VISÃO GERAL</span>
                <h3>Dashboard</h3>
                <p>
                    O Dashboard reúne indicadores das operações ferroviárias
                    e informações importantes para acompanhar o sistema.
                </p>
                <h4>Principais informações</h4>
                <ul>
                    <li>Condições meteorológicas de Joinville, SC.</li>
                    <li>Quantidade de trens em operação.</li>
                    <li>Quantidade de sensores e rotas ativas.</li>
                    <li>Quantidade de usuários ativos.</li>
                    <li>Alertas e informações dos trens cadastrados.</li>
                </ul>
                <h4>Como utilizar?</h4>
                <p>
                    Acesse o Dashboard para consultar os indicadores
                    e identificar situações que precisam de atenção.
                </p>
                <button type="button" class="helg-botao" onclick="mostrarConteudo('dashboard')">
                    Acessar Dashboard
                </button>
            </div>
        </article>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/Alertas.png" alt="Tela Alertas e Falhas" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">03 / OCORRÊNCIAS</span>
                <h3>Alertas e Falhas</h3>
                <p>
                    Essa área apresenta ocorrências identificadas no sistema,
                    como trens em manutenção ou inativos e sensores inativos.
                </p>
                <h4>Principais informações</h4>
                <ul>
                    <li>Tipo da ocorrência.</li>
                    <li>Descrição do alerta.</li>
                    <li>Nível de severidade.</li>
                </ul>
                <h4>Como utilizar?</h4>
                <p>
                    Consulte a lista de alertas para identificar
                    equipamentos que precisam de verificação.
                </p>
                <button type="button" class="helg-botao" onclick="mostrarConteudo('alertas')">
                    Consultar Alertas
                </button>
            </div>
        </article>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/relatorios.png" alt="Tela Relatórios" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">04 / DOCUMENTAÇÃO</span>
                <h3>Relatórios</h3>
                <p>
                    A área de Relatórios permite gerar documentos com
                    informações registradas na plataforma.
                </p>
                <h4>Como utilizar?</h4>
                <ol>
                    <li>Selecione o tipo de relatório desejado.</li>
                    <li>Escolha o período dos registros.</li>
                    <li>Defina o formato PDF ou CSV.</li>
                    <li>Clique em Gerar Relatório.</li>
                </ol>
                <button type="button" class="helg-botao" onclick="mostrarConteudo('relatorios')">
                    Acessar Relatórios
                </button>
            </div>
        </article>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/monitorarT.png" alt="Tela Monitorar Trens" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">05 / MONITORAMENTO</span>
                <h3>Monitorar Trens</h3>
                <p>
                    Essa funcionalidade permite consultar os dados de um
                    trem cadastrado e visualizar informações relacionadas
                    à sua rota no mapa de monitoramento.
                </p>
                <h4>Principais informações</h4>
                <ul>
                    <li>Código e modelo do trem selecionado.</li>
                    <li>Velocidade cadastrada.</li>
                    <li>Status do trem.</li>
                    <li>Rota vinculada ao trem.</li>
                    <li>Mapa de monitoramento.</li>
                </ul>
                <h4>Como utilizar?</h4>
                <p>
                    Selecione um trem na lista para visualizar seus
                    dados e acompanhar as informações disponíveis.
                </p>
                <button type="button" class="helg-botao" onclick="mostrarConteudo('monitora')">
                    Monitorar Trens
                </button>
            </div>
        </article>

        <div class="helg-divisao">
            <span class="helg-etiqueta">ADMINISTRAÇÃO</span>
            <h2>Gerenciamento do sistema</h2>
            <p>
                As próximas funcionalidades são destinadas ao gerenciamento
                dos registros da plataforma. As operações administrativas
                são restritas aos usuários autorizados.
            </p>
        </div>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/gerenciarT.png" alt="Tela Gerenciar Trens" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">06 / ADMINISTRAÇÃO</span>
                <h3>Gerenciar Trens</h3>
                <p>
                    Permite cadastrar, consultar, editar e excluir
                    trens registrados no sistema.
                </p>
                <h4>Principais recursos</h4>
                <ul>
                    <li>Cadastro de novos trens.</li>
                    <li>Consulta de código, modelo, velocidade e status.</li>
                    <li>Definição e alteração do usuário responsável.</li>
                    <li>Edição e exclusão de registros.</li>
                </ul>
                <p>
                    Para cadastrar um trem, utilize o botão Novo Trem.
                    Para modificar um registro existente, utilize Editar.
                    Trens vinculados a rotas ativas não podem ser excluídos.
                </p>
                <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                    <button type="button" class="helg-botao" onclick="mostrarConteudo('gerenciarTrens')">
                        Gerenciar Trens
                    </button>
                <?php else: ?>
                    <span class="helg-acesso-restrito">Acesso administrativo</span>
                <?php endif; ?>
            </div>
        </article>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/gerenciarRota.png" alt="Tela Gerenciar Rotas" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">07 / ADMINISTRAÇÃO</span>
                <h3>Gerenciar Rotas</h3>
                <p>
                    Permite organizar as rotas ferroviárias cadastradas,
                    incluindo os pontos de saída, chegada e paradas.
                </p>
                <h4>Como utilizar?</h4>
                <ol>
                    <li>Clique em Nova Rota para realizar um cadastro.</li>
                    <li>Informe os dados e as paradas da rota.</li>
                    <li>Consulte os registros na tabela.</li>
                    <li>Utilize Editar ou Excluir quando necessário.</li>
                </ol>
                <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                    <button type="button" class="helg-botao" onclick="mostrarConteudo('gerenciarRotas')">
                        Gerenciar Rotas
                    </button>
                <?php else: ?>
                    <span class="helg-acesso-restrito">Acesso administrativo</span>
                <?php endif; ?>
            </div>
        </article>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/gerencairS.png" alt="Tela Gerenciar Sensores" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">08 / ADMINISTRAÇÃO</span>
                <h3>Gerenciar Sensores</h3>
                <p>
                    Essa área permite administrar os sensores cadastrados
                    na plataforma e consultar suas informações.
                </p>
                <h4>Principais recursos</h4>
                <ul>
                    <li>Cadastro de novos sensores.</li>
                    <li>Consulta da localização e do tipo de sensor.</li>
                    <li>Visualização do endereço e status.</li>
                    <li>Edição e exclusão dos registros.</li>
                </ul>
                <p>
                    Utilize Novo Sensor para cadastrar um equipamento
                    ou os botões da tabela para editar e excluir registros.
                </p>
                <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                    <button type="button" class="helg-botao" onclick="mostrarConteudo('gerenciar')">
                        Gerenciar Sensores
                    </button>
                <?php else: ?>
                    <span class="helg-acesso-restrito">Acesso administrativo</span>
                <?php endif; ?>
            </div>
        </article>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/gerenciarU.png" alt="Tela Gerenciar Usuários" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">09 / ADMINISTRAÇÃO</span>
                <h3>Gerenciar Usuários</h3>
                <p>
                    Permite administrar as contas cadastradas na
                    plataforma e consultar seus dados.
                </p>
                <h4>Principais recursos</h4>
                <ul>
                    <li>Cadastro de novos usuários.</li>
                    <li>Consulta de nome, CPF, e-mail e status.</li>
                    <li>Alteração dos dados cadastrados.</li>
                    <li>Exclusão de usuários quando permitida.</li>
                </ul>
                <p>
                    Para cadastrar uma conta, clique em Novo Usuário.
                    Usuários responsáveis por trens precisam ter esse
                    vínculo alterado antes de serem excluídos.
                </p>
                <?php if (($_SESSION['perfil'] ?? '') === 'admin'): ?>
                    <button type="button" class="helg-botao" onclick="mostrarConteudo('gerenciarUsuario')">
                        Gerenciar Usuários
                    </button>
                <?php else: ?>
                    <span class="helg-acesso-restrito">Acesso administrativo</span>
                <?php endif; ?>
            </div>
        </article>

        <article class="helg-recurso">
            <div class="helg-recurso-imagem">
                <img src="./assets/img/tutorial/sair.png" alt="Tela Sair do Sistema" loading="lazy">
            </div>
            <div class="helg-recurso-texto">
                <span class="helg-etiqueta">10 / SEGURANÇA</span>
                <h3>Sair do Sistema</h3>
                <p>
                    A opção Sair permite encerrar a sessão do usuário
                    e retornar à tela de login.
                </p>
                <h4>Como utilizar?</h4>
                <ol>
                    <li>Clique em Sair no menu superior.</li>
                    <li>Selecione Confirmar para encerrar a sessão.</li>
                    <li>Ou clique em Cancelar para continuar utilizando o sistema.</li>
                </ol>
                <button type="button" class="helg-botao" onclick="mostrarConteudo('sair')">
                    Ir para Sair
                </button>
            </div>
        </article>
    </section>
</div>




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


            <br>
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

            <?php if (($_GET['sucesso'] ?? '') === 'usuario_atualizado'): ?>

    <div id="mensagem-usuario-atualizado" class="alert alert-success text-center" role="alert">
    Usuário atualizado com sucesso!
    </div>
<?php endif; ?>


    <div class="col-md-12">

            <h2>Gerenciar Usuário</h2>


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
    <a class="btn-sair" href="./backend/logout.php">Confirmar</a>
    <button class="btn-sair" type="button" onclick="mostrarConteudo('home')">Cancelar</button>
        </div>

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

</body>
</html>