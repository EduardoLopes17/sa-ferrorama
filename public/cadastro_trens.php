
<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../infra/conexao.php';

$erro = '';
$nome_responsavel = '';

$id_usuario = (int) $_SESSION['usuario_id'];

$sql_usuario = "SELECT nome FROM usuarios WHERE id = ?";
$stmt_usuario = $conexao->prepare($sql_usuario);
$stmt_usuario->bind_param("i", $id_usuario);
$stmt_usuario->execute();

$resultado_usuario = $stmt_usuario->get_result();
$usuario = $resultado_usuario->fetch_assoc();
$stmt_usuario->close();

if (!$usuario) {
    header('Location: login.php');
    exit;
}

$nome_responsavel = $usuario['nome'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (($_SESSION['perfil'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Acesso negado.');
    }

    $codigo = trim($_POST['codigo'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $velocidade = trim($_POST['velocidade'] ?? '');
    $status = $_POST['status'] ?? 'Parado';

    $status_permitidos = [
        'Em Operação',
        'Parado',
        'Manutenção',
        'Inativo'
    ];

    if (
        $codigo === '' ||
        $modelo === '' ||
        strlen($codigo) > 30 ||
        strlen($modelo) > 100 ||
        !is_numeric($velocidade) ||
        (float) $velocidade < 0 ||
        (float) $velocidade > 9999.99 ||
        !in_array($status, $status_permitidos, true)
    ) {
        $erro = 'Preencha os campos corretamente.';
    } else {

        $sql = "INSERT INTO trens
                (codigo, modelo, velocidade, status, usuario_responsavel_id)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);

        $velocidade_numero = (float) $velocidade;

        $stmt->bind_param(
            "ssdsi",
            $codigo,
            $modelo,
            $velocidade_numero,
            $status,
            $id_usuario
        );

        try {
            $stmt->execute();
            $stmt->close();

            header('Location: ../index.php?trem_cadastrado=sucesso');
            exit;

        } catch (mysqli_sql_exception $e) {
            $erro = $e->getCode() == 1062
                ? 'Já existe um trem com esse código.'
                : 'Não foi possível cadastrar o trem.';
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Trem - Ferrorama</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body class="pagina-cadastro-sensor">

    <div class="formulario-cadastro-sensor">

        <h2>Cadastro de Trem</h2>

        <?php if ($erro !== '') { ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php } ?>

        <form method="POST">

            <div class="campos-cadastro-sensor">

                <div>
                    <label for="codigo">Código do Trem</label>
                    <input type="text" id="codigo" name="codigo"
                           placeholder="Ex.: TR-2401"
                           maxlength="30" required
                           value="<?= htmlspecialchars($codigo ?? '') ?>">
                </div>

                <div>
                    <label for="modelo">Modelo do Trem</label>
                    <input type="text" id="modelo" name="modelo"
                           placeholder="Ex.: Trem-bala"
                           maxlength="100" required
                           value="<?= htmlspecialchars($modelo ?? '') ?>">
                </div>

                <div>
                    <label for="velocidade">Velocidade (km/h)</label>
                    <input type="number" id="velocidade"
                           name="velocidade"
                           min="0" max="9999.99" step="0.01"
                           value="<?= htmlspecialchars($velocidade ?? '0') ?>"
                           required>
                </div>

                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <?php foreach (['Parado', 'Em Operação', 'Manutenção', 'Inativo'] as $opcao) { ?>
                            <option value="<?= htmlspecialchars($opcao) ?>"
                                <?= ($status ?? 'Parado') === $opcao ? 'selected' : '' ?>>
                                <?= htmlspecialchars($opcao) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label for="responsavel">Funcionário Responsável</label>
                    <input type="text" id="responsavel"
                           value="<?= htmlspecialchars($nome_responsavel) ?>"
                           readonly>
                </div>

            </div>

            <button type="submit" class="botao-cadastrar-sensor">
                Cadastrar Trem
            </button>

            <a href="../index.php" class="voltar-cadastro-sensor">
                Voltar
            </a>

        </form>
    </div>

</body>
</html>
