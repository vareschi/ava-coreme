<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../sign-in.php");
    exit;
}

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('turmas');

$pdo = getPDO();

// Coleta os dados do formulário
$id              = $_POST['id'] ?? null; // pode ser vazio para nova turma
$nome            = trim($_POST['nome'] ?? '');
$especialidade   = $_POST['especialidade_id'] ?? null;
$preceptor       = $_POST['preceptor_id'] ?? null; // opcional
$data_abertura   = $_POST['data_abertura'] ?? null;
$data_fechamento = $_POST['data_fechamento'] ?? null;
$descricao       = trim($_POST['descricao'] ?? '');
$carga_horaria   = $_POST['carga_horaria'] ?? null;
$vagas           = $_POST['vagas'] ?? null;

// Validação básica
if (empty($nome) || !$especialidade || !$data_abertura || !$data_fechamento) {
    header("Location: ../turmas.php?erro=1");
    exit;
}

if ($id) {
    // Atualiza
    $stmt = $pdo->prepare("
        UPDATE turmas SET 
            nome = ?, 
            especialidade_id = ?, 
            preceptor_id = ?, 
            data_abertura = ?, 
            data_fechamento = ?, 
            descricao = ?, 
            carga_horaria = ?, 
            vagas = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $nome,
        $especialidade,
        $preceptor ?: null,
        $data_abertura,
        $data_fechamento,
        $descricao,
        $carga_horaria,
        $vagas,
        $id
    ]);
} else {
    // Insere nova
    $stmt = $pdo->prepare("
        INSERT INTO turmas 
        (nome, especialidade_id, preceptor_id, data_abertura, data_fechamento, descricao, carga_horaria, vagas, status) 
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, 1)
    ");
    $stmt->execute([
        $nome,
        $especialidade,
        $preceptor ?: null,
        $data_abertura,
        $data_fechamento,
        $descricao,
        $carga_horaria,
        $vagas
    ]);
}

header("Location: ../turmas.php?sucesso=1");
exit;
