
<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once '../infra/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $localizacao = trim($_POST['localizacao'] ?? '');
    $tipo_sensor = trim($_POST['tipo_sensor'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $status = $_POST['status'] ?? 'Ativo';

    if (
        $nome === '' ||
        $localizacao === '' ||
        $tipo_sensor === '' ||
        $endereco === ''
    ) {
        $erro = 'Preencha todos os campos.';
    } elseif (!in_array($status, ['Ativo', 'Inativo'], true)) {
        $erro = 'Status inválido.';
    } else {
        $sql = "INSERT INTO sensores
                (nome, localizacao, tipo_sensor, endereco, status)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param(
            "sssss",
            $nome,
            $localizacao,
            $tipo_sensor,
            $endereco,
            $status
        );

        if ($stmt->execute()) {
            $stmt->close();
            header('Location: ../index.php?cadastro_sensor=sucesso');
            exit;
        }

        $erro = 'Não foi possível cadastrar o sensor.';
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Sensor</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <link rel="stylesheet" href="../assets/style/style.css">
</head>

<body>
   
<body class="pagina-cadastro-sensor">

    <div class="formulario-cadastro-sensor">

        <h2>Cadastro de Sensor</h2>

        <?php if ($erro !== '') { ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php } ?>

        <form method="POST" action="">

            <div class="campos-cadastro-sensor">

                <div>
                    <label for="nome">Nome do Sensor</label>
                    <input type="text" id="nome" name="nome"
                           placeholder="Ex.: Sensor 01"
                           maxlength="100" required
                           value="<?= htmlspecialchars($nome ?? '') ?>">
                </div>

                <div>
                    <label for="localizacao">Localização da Plataforma</label>
                    <input type="text" id="localizacao" name="localizacao"
                           placeholder="Ex.: Plataforma 1"
                           maxlength="150" required
                           value="<?= htmlspecialchars($localizacao ?? '') ?>">
                </div>

                <div>
                    <label for="tipo_sensor">Tipo de Sensor</label>
                    <select id="tipo_sensor" name="tipo_sensor" required>
                        <option value="">Selecione o tipo de sensor</option>
                        <?php foreach (['Temperatura', 'Movimento', 'Umidade', 'Vibração', 'Proximidade'] as $tipo) { ?>
                            <option value="<?= htmlspecialchars($tipo) ?>"
                                <?= ($tipo_sensor ?? '') === $tipo ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tipo) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div>
                    <label for="endereco">Endereço</label>
                    <input type="text" id="endereco" name="endereco"
                           placeholder="Ex.: Rua Urussanga"
                           maxlength="200" required
                           value="<?= htmlspecialchars($endereco ?? '') ?>">
                </div>

                <div>
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Ativo"
                            <?= ($status ?? 'Ativo') === 'Ativo' ? 'selected' : '' ?>>
                            Ativo
                        </option>
                        <option value="Inativo"
                            <?= ($status ?? '') === 'Inativo' ? 'selected' : '' ?>>
                            Inativo
                        </option>
                    </select>
                </div>

            </div>

            <button type="submit" class="botao-cadastrar-sensor">
                Cadastrar Sensor
            </button>

            <a href="../index.php" class="voltar-cadastro-sensor">
                Voltar
            </a>

        </form>
    </div>

</body>
</html>
