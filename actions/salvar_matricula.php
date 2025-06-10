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

$usuario_id = $_POST['usuario_id'] ?? null;
$turma_id   = $_POST['turma_id'] ?? null;
$edital_origem_id = $_POST['edital_origem_id'] ?? null;

if (!$usuario_id || !$turma_id) {
    header("Location: ../matriculas.php?erro=campos_obrigatorios");
    exit;
}

// Verifica se já existe a matrícula
$stmt = $pdo->prepare("SELECT id FROM matriculas WHERE usuario_id = ? AND turma_id = ? AND data_exclusao IS NULL");
$stmt->execute([$usuario_id, $turma_id]);

if ($stmt->fetch()) {
    header("Location: ../matriculas.php?erro=ja_matriculado");
    exit;
}

// Insere nova matrícula
$stmt = $pdo->prepare("INSERT INTO matriculas (turma_id, usuario_id, edital_origem_id, status) VALUES (?, ?, ?, 1)");
$stmt->execute([$turma_id, $usuario_id, $edital_origem_id ?: null]);

header("Location: ../matriculas.php?sucesso=1");
exit;
