<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: sign-in.php");
    exit;
}

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

// Coleta os dados do formulário
$id = $_POST['id'] ?? null;
$avaliacao_id = $_POST['avaliacao_id'] ?? null;
$titulo = trim($_POST['titulo'] ?? '');

// Validação básica
if (!$id || !$avaliacao_id || $titulo === '') {
    header("Location: ../cadastro_avaliacao.php?id=" . urlencode($avaliacao_id));
    exit;
}

// Atualiza a pergunta
$stmt = $pdo->prepare("UPDATE avaliacoes_perguntas SET titulo = ? WHERE id = ?");
$stmt->execute([$titulo, $id]);

header("Location: ../cadastro_avaliacao.php?id=" . urlencode($avaliacao_id));
exit;
