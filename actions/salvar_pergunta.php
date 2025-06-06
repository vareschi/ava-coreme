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

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

$avaliacao_id = $_POST['avaliacao_id'] ?? null;
$titulo = trim($_POST['titulo'] ?? '');

if (!$avaliacao_id || $titulo === '') {
    header("Location: ../cadastro_avaliacao.php?id=" . urlencode($avaliacao_id));
    exit;
}

$stmt = $pdo->prepare("INSERT INTO avaliacoes_perguntas (avaliacao_id, titulo) VALUES (?, ?)");
$stmt->execute([$avaliacao_id, $titulo]);

header("Location: ../cadastro_avaliacao.php?id=" . urlencode($avaliacao_id));
exit;
