
<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once '../infra/conexao.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header('Location: ../index.php?erro_sensor=id');
    exit;
}

$id = (int) $_GET['id'];

$sql = "SELECT id, nome, localizacao, tipo_sensor, endereco, status
        FROM sensores WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$sensor = $resultado->fetch_assoc();

$stmt->close();

if (!$sensor) {
    header('Location: ../index.php?erro_sensor=nao_encontrado');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sensor - Ferrorama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body class="pagina-cadastro-sensor">

    <div class="formulario-cadastro-sensor">

        <h2>Editar Sensor</h2>

        <form action="../backend/atualizar_sensor.php" method="POST">

            <input type="hidden" name="id"
                   value="<?= htmlspecialchars($sensor['id']) ?>">

            <div class="campos-cadastro-sensor">

                <div>
                    <label for="nome">Nome do Sensor</label>
                    <input type="text" id="nome" name="nome"
                           maxlength="100" required
                           value="<?= htmlspecialchars($sensor['nome']) ?>">
                </div>

                <div>
                    <label for="localizacao">Localização da Plataforma</label>
                    <input type="text" id="localizacao" name="localizacao"
                           maxlength="150" required
                           value="<?= htmlspecialchars($sensor['localizacao']) ?>">
                </div>

                <div>
                    <label for="tipo_sensor">Tipo de Sensor</label>
                    <select id="tipo_sensor" name="tipo_sensor" required>
                        <?php foreach (['Temperatura', 'Movimento', 'Umidade', 'Vibração', 'Proximidade'] as $tipo) { ?>
                            <option value="<?= htmlspecialchars($tipo) ?>"
                                <?= $sensor['tipo_sensor'] === $tipo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipo) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" name="endereco"
                           maxlength="200" required
                           value="<?= htmlspecialchars($sensor['endereco']) ?>">
                </div>

                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Ativo"
                            <?= $sensor['status'] === 'Ativo' ? 'selected' : '' ?>>
                            Ativo
                        </option>
                        <option value="Inativo"
                            <?= $sensor['status'] === 'Inativo' ? 'selected' : '' ?>>
                            Inativo
                        </option>
                    </select>
                </div>

            </div>

            <button type="submit" class="botao-cadastrar-sensor">
                Salvar Alterações
            </button>

            <a href="../index.php" class="voltar-cadastro-sensor">
                Voltar
            </a>

        </form>
    </div>

</body>
</html>
