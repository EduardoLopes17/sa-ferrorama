
<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../infra/conexao.php';

// Buscar o ID do trem
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../index.php?erro_trem=id');
    exit;
}

// Buscar as informações do trem
$sql = "SELECT id, codigo, modelo, velocidade, status,
               usuario_responsavel_id
        FROM trens
        WHERE id = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$trem = $resultado->fetch_assoc();
$stmt->close();

if (!$trem) {
    header('Location: ../index.php?erro_trem=nao_encontrado');
    exit;
}

// Verificar permissões
$eh_admin = ($_SESSION['perfil'] ?? '') === 'admin';

$eh_responsavel = (
    (int) ($trem['usuario_responsavel_id'] ?? 0) ===
    (int) $_SESSION['usuario_id']
);

if (!$eh_admin && !$eh_responsavel) {
    http_response_code(403);
    exit('Você não é responsável por este trem.');
}

// Buscar funcionários ativos (somente administrador)
$resultado_usuarios = null;

if ($eh_admin) {
    $sql_usuarios = "SELECT id, nome, email
                     FROM usuarios
                     WHERE status = 'ativo'
                     ORDER BY nome ASC";

    $resultado_usuarios = $conexao->query($sql_usuarios);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Trem - Ferrorama</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body class="pagina-cadastro-sensor">

    <div class="formulario-cadastro-sensor">

        <h2>Editar Trem</h2>

        <form action="../backend/atualizar_trem.php" method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= htmlspecialchars($trem['id']) ?>"
            >

            <div class="campos-cadastro-sensor">

                <?php if ($eh_admin) { ?>

                    <!-- CÓDIGO DO TREM -->
                    <div>
                        <label for="codigo">Código do Trem</label>

                        <input
                            type="text"
                            id="codigo"
                            name="codigo"
                            maxlength="30"
                            required
                            value="<?= htmlspecialchars($trem['codigo']) ?>"
                        >
                    </div>

                    <!-- MODELO DO TREM -->
                    <div>
                        <label for="modelo">Modelo do Trem</label>

                        <input
                            type="text"
                            id="modelo"
                            name="modelo"
                            maxlength="100"
                            required
                            value="<?= htmlspecialchars($trem['modelo']) ?>"
                        >
                    </div>

                <?php } else { ?>

                    <!-- USUÁRIO NORMAL: SOMENTE VISUALIZA -->
                    <div>
                        <label>Código do Trem</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars($trem['codigo']) ?>"
                            readonly
                        >
                    </div>

                    <div>
                        <label>Modelo do Trem</label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars($trem['modelo']) ?>"
                            readonly
                        >
                    </div>

                <?php } ?>

                <!-- VELOCIDADE -->
                <div>
                    <label for="velocidade">Velocidade (km/h)</label>

                    <input
                        type="number"
                        id="velocidade"
                        name="velocidade"
                        min="0"
                        max="9999.99"
                        step="0.01"
                        required
                        value="<?= htmlspecialchars($trem['velocidade']) ?>"
                    >
                </div>

                <!-- STATUS -->
                <div>
                    <label for="status">Status</label>

                    <select id="status" name="status" required>

                        <?php
                        $status_permitidos = [
                            'Parado',
                            'Em Operação',
                            'Manutenção',
                            'Inativo'
                        ];

                        foreach ($status_permitidos as $opcao) {
                        ?>

                            <option
                                value="<?= htmlspecialchars($opcao) ?>"
                                <?= $trem['status'] === $opcao ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($opcao) ?>
                            </option>

                        <?php } ?>

                    </select>
                </div>

                <?php if ($eh_admin) { ?>

                    <!-- FUNCIONÁRIO RESPONSÁVEL -->
                    <div>
                        <label for="usuario_responsavel_id">
                            Funcionário Responsável
                        </label>

                        <select
                            id="usuario_responsavel_id"
                            name="usuario_responsavel_id"
                            required
                        >

                            <option value="">
                                Selecione um funcionário
                            </option>

                            <?php
                            while ($usuario = $resultado_usuarios->fetch_assoc()) {
                            ?>

                                <option
                                    value="<?= htmlspecialchars($usuario['id']) ?>"
                                    <?= $trem['usuario_responsavel_id'] == $usuario['id']
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= htmlspecialchars($usuario['nome']) ?>
                                    - <?= htmlspecialchars($usuario['email']) ?>
                                </option>

                            <?php } ?>

                        </select>
                    </div>

                <?php } ?>

            </div>

            <!-- BOTÃO SALVAR -->
            <button
                type="submit"
                class="botao-cadastrar-sensor"
            >
                Salvar Alterações
            </button>

            <a
                href="../index.php"
                class="voltar-cadastro-sensor"
            >
                Voltar
            </a>

        </form>

    </div>

</body>
</html>
