
<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once '../infra/conexao.php';

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

$sql_trens_disponiveis = "SELECT id, codigo, modelo
                         FROM trens
                         ORDER BY codigo ASC";

$resultado_trens_disponiveis = $conexao->query($sql_trens_disponiveis);

$bairros = [
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
    'Vila Nova',
];

$erro = '';
$nome = '';
$status = 'Ativa';
$trem_id = 0;
$saida = '';
$chegada = '';
$intermediarias = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $status = $_POST['status'] ?? '';
    $trem_id = filter_var($_POST['trem_id'] ?? null, FILTER_VALIDATE_INT) ?: 0;
    $saida = $_POST['saida'] ?? '';
    $chegada = $_POST['chegada'] ?? '';
    $intermediarias = $_POST['intermediarias'] ?? [];

    if (!is_array($intermediarias)) {
        $intermediarias = [];
    }

    $paradas = array_merge(
        [$saida],
        $intermediarias,
        [$chegada]
    );

    $validas = true;

    foreach ($paradas as $parada) {
        if (!is_string($parada) || !in_array($parada, $bairros, true)) {
            $validas = false;
            break;
        }
    }

    if (
        $nome === '' ||
         $trem_id <= 0 ||
        mb_strlen($nome) > 100 ||
        !in_array($status, ['Ativa', 'Inativa'], true) ||
        !$validas ||
        count($intermediarias) > 50 ||
        count($paradas) !== count(array_unique($paradas))
    ) {
        $erro = 'Verifique os dados. As paradas devem ser válidas e não podem se repetir.';
    } else {

        try {
            $conexao->begin_transaction();

            
            $sql_rota = "INSERT INTO rotas (nome, status)
             VALUES (?, ?)";

            $stmt_rota = $conexao->prepare($sql_rota);
            $stmt_rota->bind_param("ss", $nome, $status);

            $stmt_rota->execute();

            $rota_id = $conexao->insert_id;
            $stmt_rota->close();

            $sql_parada = "INSERT INTO paradas (rota_id, nome, ordem)
                           VALUES (?, ?, ?)";

            $stmt_parada = $conexao->prepare($sql_parada);

            foreach ($paradas as $indice => $parada) {
                $ordem = $indice + 1;

                $stmt_parada->bind_param(
                    "isi",
                    $rota_id,
                    $parada,
                    $ordem
                );

                $stmt_parada->execute();
            }

            $stmt_parada->close();

            $sql_vincular_trem = "UPDATE trens
                     SET rota_id = ?
                     WHERE id = ?";

            $stmt_vincular_trem = $conexao->prepare($sql_vincular_trem);
            $stmt_vincular_trem->bind_param("ii", $rota_id, $trem_id);
            $stmt_vincular_trem->execute();

            if ($stmt_vincular_trem->affected_rows !== 1) {
            throw new mysqli_sql_exception('Trem não encontrado.');
            }

            $stmt_vincular_trem->close();
            $conexao->commit();

            header('Location: ../index.php?rota_cadastrada=sucesso');
            exit;

        } catch (mysqli_sql_exception $e) {
            $conexao->rollback();
            $erro = 'Não foi possível cadastrar a rota.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Rota - Ferrorama</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body class="pagina-cadastro-sensor pagina-cadastro-rota">

    <div class="formulario-cadastro-sensor">

        <h2>Cadastro de Rota</h2>

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
                        placeholder="Ex.: Linha Centro - Vila Nova"
                        value="<?= htmlspecialchars($nome) ?>"
                        required
                    >
                </div>

                <div>
                    <label for="status">Status</label>

                    <select id="status" name="status" required>
                        <option value="Ativa" <?= $status === 'Ativa' ? 'selected' : '' ?>>
                            Ativa
                        </option>

                        <option value="Inativa" <?= $status === 'Inativa' ? 'selected' : '' ?>>
                            Inativa
                        </option>
                    </select>
                </div>

                    <div>
                        <label for="trem_id">Trem Responsável</label>

                    <select id="trem_id" name="trem_id">
                    <option value="">Selecione um trem</option>

                        <?php while ($trem = $resultado_trens_disponiveis->fetch_assoc()) { ?>
                            <option value="<?= (int) $trem['id'] ?>">
                        <?= htmlspecialchars($trem['codigo'] . ' - ' . $trem['modelo']) ?>
                            </option>
                        <?php } ?>
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
                Cadastrar Rota
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
