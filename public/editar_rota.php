
<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['perfil'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acesso negado.');
}

require_once __DIR__ . '/../infra/conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    exit('Rota inválida.');
}

$bairros = [
    'Adhemar Garcia', 'América', 'Anita Garibaldi', 'Atiradores',
    'Aventureiro', 'Boa Vista', 'Boehmerwald', 'Bom Retiro',
    'Bucarein', 'Centro', 'Comasa', 'Costa e Silva',
    'Dona Francisca', 'Espinheiros', 'Fátima', 'Floresta',
    'Glória', 'Guanabara', 'Iririú', 'Itaum', 'Itinga',
    'Jardim Iririú', 'Jardim Paraíso', 'Jardim Sofia',
    'Jarivatuba', 'João Costa', 'Morro do Meio',
    'Nova Brasília', 'Paranaguamirim', 'Parque Guarani',
    'Petrópolis', 'Pirabeiraba', 'Profipo', 'Rio Bonito',
    'Saguaçu', 'Santa Catarina', 'Santo Antônio',
    'São Marcos', 'Ulysses Guimarães', 'Vila Cubatão',
    'Vila Nova', 'Zona Industrial Norte', 'Zona Industrial Tupy'
];

$stmt = $conexao->prepare(
    "SELECT id, nome, status FROM rotas WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$rota = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$rota) {
    exit('Rota não encontrada.');
}

$stmt = $conexao->prepare(
    "SELECT nome FROM paradas WHERE rota_id = ? ORDER BY ordem ASC"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

$paradas = [];

while ($linha = $resultado->fetch_assoc()) {
    $paradas[] = $linha['nome'];
}

$stmt->close();

$nome = $rota['nome'];
$status = $rota['status'];
$saida = $paradas[0] ?? '';
$chegada = count($paradas) >= 2 ? $paradas[count($paradas) - 1] : '';
$intermediarias = array_slice($paradas, 1, -1);
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $status = $_POST['status'] ?? '';
    $saida = $_POST['saida'] ?? '';
    $chegada = $_POST['chegada'] ?? '';
    $intermediarias = $_POST['intermediarias'] ?? [];

    if (!is_array($intermediarias)) {
        $intermediarias = [];
    }

    $novas_paradas = array_merge([$saida], $intermediarias, [$chegada]);
    $validas = true;

    foreach ($novas_paradas as $parada) {
        if (!is_string($parada) || !in_array($parada, $bairros, true)) {
            $validas = false;
            break;
        }
    }

    if (
        $nome === '' ||
        mb_strlen($nome) > 100 ||
        !in_array($status, ['Ativa', 'Inativa'], true) ||
        !$validas ||
        count($intermediarias) > 50 ||
        count($novas_paradas) !== count(array_unique($novas_paradas))
    ) {
        $erro = 'Verifique os dados. As paradas devem ser válidas e não podem se repetir.';
    } else {
        try {
            $conexao->begin_transaction();

            $stmt = $conexao->prepare(
                "UPDATE rotas SET nome = ?, status = ? WHERE id = ?"
            );
            $stmt->bind_param("ssi", $nome, $status, $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conexao->prepare(
                "DELETE FROM paradas WHERE rota_id = ?"
            );
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $conexao->prepare(
                "INSERT INTO paradas (rota_id, nome, ordem) VALUES (?, ?, ?)"
            );

            foreach ($novas_paradas as $indice => $parada) {
                $ordem = $indice + 1;
                $stmt->bind_param("isi", $id, $parada, $ordem);
                $stmt->execute();
            }

            $stmt->close();
            $conexao->commit();

            header('Location: ../index.php?rota_editada=sucesso');
            exit;

        } catch (mysqli_sql_exception $e) {
            $conexao->rollback();
            $erro = 'Não foi possível atualizar a rota.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Rota - Ferrorama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body class="pagina-cadastro-sensor pagina-cadastro-rota">

    <div class="formulario-cadastro-sensor">

        <h2>Editar Rota</h2>

        <?php if ($erro !== '') { ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="campos-cadastro-sensor">

                <div>
                    <label for="nome">Nome da Rota</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        maxlength="100"
                        value="<?= htmlspecialchars($nome) ?>"
                        required
                    >
                </div>

                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Ativa" <?= $status === 'Ativa' ? 'selected' : '' ?>>Ativa</option>
                        <option value="Inativa" <?= $status === 'Inativa' ? 'selected' : '' ?>>Inativa</option>
                    </select>
                </div>

                <div>
                    <label for="saida">Ponto de Saída</label>
                    <select id="saida" name="saida" required>
                        <option value="">Selecione o ponto de saída</option>
                        <?php foreach ($bairros as $bairro) { ?>
                            <option
                                value="<?= htmlspecialchars($bairro) ?>"
                                <?= $saida === $bairro ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($bairro) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label for="chegada">Ponto de Chegada</label>
                    <select id="chegada" name="chegada" required>
                        <option value="">Selecione o ponto de chegada</option>
                        <?php foreach ($bairros as $bairro) { ?>
                            <option
                                value="<?= htmlspecialchars($bairro) ?>"
                                <?= $chegada === $bairro ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($bairro) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

            </div>

            <div class="secao-paradas-rota">
                <h3>Paradas Intermediárias</h3>

                <div id="lista-paradas">
                    <?php foreach ($intermediarias as $parada_atual) { ?>
                        <div class="linha-parada">
                            <select name="intermediarias[]" required>
                                <option value="">Selecione uma parada</option>

                                <?php foreach ($bairros as $bairro) { ?>
                                    <option
                                        value="<?= htmlspecialchars($bairro) ?>"
                                        <?= $parada_atual === $bairro ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($bairro) ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <button type="button" class="btn-remover-parada">
                                Remover
                            </button>
                        </div>
                    <?php } ?>
                </div>

                <button
                    type="button"
                    id="adicionar-parada"
                    class="btn-adicionar-parada"
                >
                    + Adicionar Parada
                </button>
            </div>

            <button type="submit" class="botao-cadastrar-sensor">
                Salvar Alterações
            </button>

            <a href="../index.php" class="voltar-cadastro-sensor">
                Voltar
            </a>

        </form>
    </div>

    <template id="modelo-parada">
        <div class="linha-parada">
            <select name="intermediarias[]" required>
                <option value="">Selecione uma parada</option>

                <?php foreach ($bairros as $bairro) { ?>
                    <option value="<?= htmlspecialchars($bairro) ?>">
                        <?= htmlspecialchars($bairro) ?>
                    </option>
                <?php } ?>
            </select>

            <button type="button" class="btn-remover-parada">
                Remover
            </button>
        </div>
    </template>

    <script src="../script/cadastro_rotas.js"></script>
</body>
</html>
