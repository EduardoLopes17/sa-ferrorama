
<?php

<?php

require_once __DIR__ . '/../infra/verificar_admin.php';
require_once __DIR__ . '/../infra/conexao.php';


session_start();

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../public/login.php');
    exit;
}

require_once __DIR__ . '/../infra/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: ../index.php?erro_trem=id');
    exit;
}

// Buscar o trem e seu responsável
$sql_trem = "SELECT id, usuario_responsavel_id
             FROM trens
             WHERE id = ?";

$stmt_trem = $conexao->prepare($sql_trem);
$stmt_trem->bind_param("i", $id);
$stmt_trem->execute();

$trem = $stmt_trem->get_result()->fetch_assoc();
$stmt_trem->close();

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
    exit('Você não tem permissão para editar este trem.');
}

// Validar velocidade e status
$velocidade = trim($_POST['velocidade'] ?? '');
$status = trim($_POST['status'] ?? '');

$status_permitidos = [
    'Em Operação',
    'Parado',
    'Manutenção',
    'Inativo'
];

if (
    !is_numeric($velocidade) ||
    (float) $velocidade < 0 ||
    (float) $velocidade > 9999.99 ||
    !in_array($status, $status_permitidos, true)
) {
    header('Location: ../index.php?erro_trem=dados_invalidos');
    exit;
}

$velocidade_numero = (float) $velocidade;

try {

    if ($eh_admin) {

        // Administrador pode alterar todos os dados
        $codigo = trim($_POST['codigo'] ?? '');
        $modelo = trim($_POST['modelo'] ?? '');

        $responsavel_id = filter_var(
            $_POST['usuario_responsavel_id'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $codigo === '' ||
            $modelo === '' ||
            strlen($codigo) > 30 ||
            strlen($modelo) > 100 ||
            !$responsavel_id
        ) {
            header('Location: ../index.php?erro_trem=dados_invalidos');
            exit;
        }

        // Verificar se o novo responsável está ativo
        $sql_usuario = "SELECT id
                        FROM usuarios
                        WHERE id = ? AND status = 'ativo'";

        $stmt_usuario = $conexao->prepare($sql_usuario);
        $stmt_usuario->bind_param("i", $responsavel_id);
        $stmt_usuario->execute();

        $usuario = $stmt_usuario->get_result()->fetch_assoc();
        $stmt_usuario->close();

        if (!$usuario) {
            header('Location: ../index.php?erro_trem=responsavel_invalido');
            exit;
        }

        $sql = "UPDATE trens
                SET codigo = ?,
                    modelo = ?,
                    velocidade = ?,
                    status = ?,
                    usuario_responsavel_id = ?
                WHERE id = ?";

        $stmt = $conexao->prepare($sql);

        $stmt->bind_param(
            "ssdsii",
            $codigo,
            $modelo,
            $velocidade_numero,
            $status,
            $responsavel_id,
            $id
        );

    } else {

        // Usuário normal altera apenas velocidade e status
        // Somente se continuar sendo responsável pelo trem
        $sql = "UPDATE trens
                SET velocidade = ?,
                    status = ?
                WHERE id = ?
                  AND usuario_responsavel_id = ?";

        $stmt = $conexao->prepare($sql);

        $id_usuario = (int) $_SESSION['usuario_id'];

        $stmt->bind_param(
            "dsii",
            $velocidade_numero,
            $status,
            $id,
            $id_usuario
        );
    }

    $stmt->execute();
    $stmt->close();

    header('Location: ../index.php?trem_atualizado=sucesso');
    exit;

} catch (mysqli_sql_exception $e) {

    if (isset($stmt)) {
        $stmt->close();
    }

    if ($e->getCode() == 1062) {
        header('Location: ../index.php?erro_trem=codigo_duplicado');
    } else {
        header('Location: ../index.php?erro_trem=atualizar');
    }

    exit;
}
