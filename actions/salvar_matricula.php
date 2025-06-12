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

verificarAcessoRecurso('matriculas');

$pdo = getPDO();

$matricula_id     = $_POST['matricula_id'] ?? null;
$usuario_id       = $_POST['usuario_id'] ?? null;
$turma_id         = $_POST['turma_id'] ?? null;
$edital_origem_id = $_POST['edital_origem_id'] ?? null;

if (!$usuario_id || !$turma_id) {
    header("Location: ../matriculas.php?erro=campos_obrigatorios");
    exit;
}

if ($matricula_id) {
    // Edição
    $stmt = $pdo->prepare("SELECT * FROM matriculas WHERE id = ?");
    $stmt->execute([$matricula_id]);
    $matriculaExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$matriculaExistente) {
        header("Location: ../matriculas.php?erro=nao_encontrado");
        exit;
    }

    // Segurança: usa o usuário original da matrícula, ignorando o que veio do POST
    $usuario_id_existente = $matriculaExistente['usuario_id'];

    // Atualiza turma e edital, mas mantém o mesmo usuário
    $stmt = $pdo->prepare("UPDATE matriculas SET turma_id = ?, edital_origem_id = ? WHERE id = ?");
    $stmt->execute([$turma_id, $edital_origem_id ?: null, $matricula_id]);

    header("Location: ../matriculas.php?sucesso=editada");
    exit;
} else {
    // NOVA MATRÍCULA

    // Verifica se já existe alguma matrícula com este usuário e turma
    $stmt = $pdo->prepare("SELECT id, status FROM matriculas WHERE usuario_id = ? AND turma_id = ?");
    $stmt->execute([$usuario_id, $turma_id]);
    $matriculaExistente = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o residente já está matriculado ativamente em outra turma
    $stmt = $pdo->prepare("SELECT id FROM matriculas WHERE usuario_id = ? AND status = 1");
    $stmt->execute([$usuario_id]);

    if ($stmt->fetch()) {
        header("Location: ../matriculas.php?erro=ja_matriculado_ativo");
        exit;
    }

    if ($matriculaExistente) {
        if ((int)$matriculaExistente['status'] === 0) {
            // Reativa matrícula inativa
            $stmt = $pdo->prepare("UPDATE matriculas SET status = 1, edital_origem_id = ?, data_exclusao = NULL WHERE id = ?");
            $stmt->execute([$edital_origem_id ?: null, $matriculaExistente['id']]);
            header("Location: ../matriculas.php?sucesso=reativada");
            exit;
        } else {
            // Já existe matrícula ativa
            header("Location: ../matriculas.php?erro=ja_matriculado");
            exit;
        }
    }

    // Insere nova matrícula
    $stmt = $pdo->prepare("INSERT INTO matriculas (turma_id, usuario_id, edital_origem_id, status) VALUES (?, ?, ?, 1)");
    $stmt->execute([$turma_id, $usuario_id, $edital_origem_id ?: null]);

    header("Location: ../matriculas.php?sucesso=1");
    exit;
}
