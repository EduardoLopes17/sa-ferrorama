
<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';

$erro = '';

$sql_usuarios = "SELECT id, nome, email
                 FROM usuarios
                 WHERE status = 'ativo'
                 ORDER BY nome ASC";

$resultado_usuarios = $conexao->query($sql_usuarios);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$responsavel_id = filter_var(
    $_POST['usuario_responsavel_id'] ?? null,
    FILTER_VALIDATE_INT
);

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
        !in_array($status, $status_permitidos, true) ||
        !$responsavel_id
    ) {
        $erro = 'Preencha os campos corretamente.';
    } else {

    $sql_verificar = "SELECT id FROM usuarios
                     WHERE id = ? AND status = 'ativo'";

    $stmt_verificar = $conexao->prepare($sql_verificar);
    $stmt_verificar->bind_param("i", $responsavel_id);
    $stmt_verificar->execute();

    $usuario_valido = $stmt_verificar->get_result()->fetch_assoc();
    $stmt_verificar->close();

    if (!$usuario_valido) {
        $erro = 'Selecione um funcionário responsável válido.';
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
    $responsavel_id
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
}
?>

<!DOCTYPE html>

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
    <label for="usuario_responsavel_id">Funcionário Responsável</label>

    <select
        id="usuario_responsavel_id"
        name="usuario_responsavel_id"
        required
    >
        <option value="">Selecione um funcionário</option>

        <?php while ($usuario = $resultado_usuarios->fetch_assoc()): ?>
            <option
                value="<?= (int) $usuario['id'] ?>"
                <?= (int) ($responsavel_id ?? 0) === (int) $usuario['id'] ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?>
                - <?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

            </div>

            <button type="submit" class="botao-cadastrar-sensor">
                Cadastrar Trem
            </button>

            <a href="../index.php?aba=gerenciarTrens" class="voltar-cadastro-sensor">
                Voltar para Gerenciar Trens
            </a>

        </form>
    </div>

</body>
</html>
