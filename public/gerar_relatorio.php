
<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../infra/conexao.php';

$tipo = $_GET['tipo'] ?? '';
$formato = $_GET['formato'] ?? '';
$periodo = $_GET['periodo'] ?? 'todos';

if ($tipo === 'usuarios' && ($_SESSION['perfil'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acesso negado. Apenas administradores podem gerar relatórios de usuários.');
}

$tipos_permitidos = ['trens', 'rotas', 'sensores', 'usuarios', 'alertas'];
$formatos_permitidos = ['csv', 'pdf'];

$tipos_permitidos = ['trens', 'rotas', 'sensores', 'usuarios', 'alertas'];
$formatos_permitidos = ['csv', 'pdf'];

if (!in_array($tipo, $tipos_permitidos, true) || !in_array($formato, $formatos_permitidos, true)) {
    http_response_code(400);
    exit('Tipo ou formato de relatório inválido.');
}

if ($periodo !== 'todos') {
    http_response_code(400);
    exit('Por enquanto, selecione Todos os registros.');
}

$titulos = [
    'trens' => 'Relatório de Trens',
    'rotas' => 'Relatório de Rotas',
    'sensores' => 'Relatório de Sensores',
    'usuarios' => 'Relatório de Usuários',
    'alertas' => 'Relatório de Alertas e Falhas'
];

$colunas = [];
$registros = [];

if ($tipo === 'trens') {
    $colunas = ['Código', 'Modelo', 'Rota', 'Velocidade (km/h)', 'Status'];

    $sql = "SELECT
        trens.codigo,
        trens.modelo,
        COALESCE(rotas.nome, 'Sem rota') AS rota,
        trens.velocidade,
        trens.status
        FROM trens
        LEFT JOIN rotas ON trens.rota_id = rotas.id
        ORDER BY trens.codigo ASC";

    $resultado = $conexao->query($sql);

    if (!$resultado) {
        http_response_code(500);
        exit('Erro ao consultar os trens.');
    }

    while ($linha = $resultado->fetch_assoc()) {
        $registros[] = [
            $linha['codigo'],
            $linha['modelo'],
            $linha['rota'],
            $linha['velocidade'],
            $linha['status']
        ];
    }
}

if ($tipo === 'rotas') {
    $colunas = ['ID', 'Nome da Rota', 'Status'];

    $sql = "SELECT id, nome, status
            FROM rotas
            ORDER BY nome ASC";

    $resultado = $conexao->query($sql);

    if (!$resultado) {
        http_response_code(500);
        exit('Erro ao consultar as rotas.');
    }

    while ($linha = $resultado->fetch_assoc()) {
        $registros[] = [
            $linha['id'],
            $linha['nome'],
            $linha['status']
        ];
    }
}

if ($tipo === 'sensores') {
    $colunas = ['ID', 'Nome', 'Tipo', 'Localização', 'Endereço', 'Status', 'Data de Cadastro'];

    $sql = "SELECT
        id,
        nome,
        tipo_sensor,
        localizacao,
        endereco,
        status,
        criado_em
        FROM sensores
        ORDER BY nome ASC";

    $resultado = $conexao->query($sql);

    if (!$resultado) {
        http_response_code(500);
        exit('Erro ao consultar os sensores.');
    }

    while ($linha = $resultado->fetch_assoc()) {
        $data = $linha['criado_em']
            ? date('d/m/Y H:i', strtotime($linha['criado_em']))
            : '—';

        $registros[] = [
            $linha['id'],
            $linha['nome'],
            $linha['tipo_sensor'],
            $linha['localizacao'],
            $linha['endereco'],
            $linha['status'],
            $data
        ];
    }
}

if ($tipo === 'usuarios') {
    $colunas = ['ID', 'Nome', 'E-mail', 'Perfil', 'Status'];

    $sql = "SELECT
        id,
        nome,
        email,
        perfil,
        status
        FROM usuarios
        ORDER BY nome ASC";

    $resultado = $conexao->query($sql);

    if (!$resultado) {
        http_response_code(500);
        exit('Erro ao consultar os usuários.');
    }

    while ($linha = $resultado->fetch_assoc()) {
        $registros[] = [
            $linha['id'],
            $linha['nome'],
            $linha['email'],
            $linha['perfil'],
            $linha['status']
        ];
    }
}

if ($tipo === 'alertas') {
    $colunas = ['Tipo', 'Equipamento', 'Descrição', 'Severidade'];

    $sql_trens = "SELECT codigo, status
                  FROM trens
                  WHERE status IN ('Manutenção', 'Inativo')
                  ORDER BY codigo ASC";

    $resultado_trens = $conexao->query($sql_trens);

    if (!$resultado_trens) {
        http_response_code(500);
        exit('Erro ao consultar os alertas dos trens.');
    }

    while ($linha = $resultado_trens->fetch_assoc()) {
        $manutencao = $linha['status'] === 'Manutenção';

        $registros[] = [
            'Trem',
            $linha['codigo'],
            $manutencao ? 'Trem em manutenção' : 'Trem inativo',
            $manutencao ? 'Alta' : 'Média'
        ];
    }

    $sql_sensores = "SELECT nome, localizacao
                    FROM sensores
                    WHERE status = 'Inativo'
                    ORDER BY nome ASC";

    $resultado_sensores = $conexao->query($sql_sensores);

    if (!$resultado_sensores) {
        http_response_code(500);
        exit('Erro ao consultar os alertas dos sensores.');
    }

    while ($linha = $resultado_sensores->fetch_assoc()) {
        $registros[] = [
            'Sensor',
            $linha['nome'],
            'Sensor inativo - Localização: ' . $linha['localizacao'],
            'Alta'
        ];
    }
}

$titulo = $titulos[$tipo];
$data_emissao = date('d/m/Y H:i');

if ($formato === 'csv') {
    $nome_arquivo = 'relatorio_' . $tipo . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');

    $arquivo = fopen('php://output', 'w');

    fwrite($arquivo, "\xEF\xBB\xBF");

    fputcsv($arquivo, $colunas, ';');

    foreach ($registros as $registro) {
        fputcsv($arquivo, $registro, ';');
    }

    fclose($arquivo);
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?> - Ferrovia União HELG</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #242424;
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
        }

        h2 {
            text-align: center;
            font-size: 20px;
            font-weight: normal;
        }

        .data {
            text-align: right;
            margin: 30px 0;
        }

        .resumo {
            background-color: #fffaf7;
            border-left: 4px solid #ff5f29;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th {
            background-color: #ff5f29;
            color: white;
            text-align: left;
        }

        th, td {
            border: 1px solid #dddddd;
            padding: 12px;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        tr:nth-child(even) {
            background-color: #fffaf7;
        }

        .acoes {
            text-align: center;
            margin-bottom: 30px;
        }

        button {
            background-color: #ff5f29;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e95420;
        }

        @media print {
            .acoes {
                display: none;
            }

            body {
                margin: 15mm;
            }

            @page {
                size: A4 landscape;
                margin: 15mm;
            }

            thead {
                display: table-header-group;
            }

            tr {
                break-inside: avoid;
            }

            th {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="acoes">
        <button type="button" onclick="window.print()">
            Salvar em PDF / Imprimir
        </button>
    </div>

    <h1>Ferrovia União HELG</h1>
    <h2><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></h2>

    <p class="data">
        Emitido em: <?= htmlspecialchars($data_emissao, ENT_QUOTES, 'UTF-8') ?>
    </p>

    <div class="resumo">
        <strong>Total de registros:</strong> <?= count($registros) ?>
    </div>

    <table>
        <thead>
            <tr>
                <?php foreach ($colunas as $coluna): ?>
                    <th><?= htmlspecialchars($coluna, ENT_QUOTES, 'UTF-8') ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php if (count($registros) > 0): ?>
                <?php foreach ($registros as $registro): ?>
                    <tr>
                        <?php foreach ($registro as $valor): ?>
                            <td><?= htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?= count($colunas) ?>">
                        Nenhum registro encontrado.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
