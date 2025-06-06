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

$pdo = getPDO();

$pergunta_id = $_POST['pergunta_id'] ?? null;
$descricao = trim($_POST['descricao'] ?? '');

if ($pergunta_id && $descricao) {
    $stmt = $pdo->prepare("INSERT INTO avaliacoes_criterios (pergunta_id, descricao) VALUES (?, ?)");
    $stmt->execute([$pergunta_id, $descricao]);
}

// Redireciona de volta para a edição da avaliação
header("Location: ../cadastro_avaliacao.php?id=" . urlencode($_GET['avaliacao_id'] ?? $_POST['avaliacao_id']));
exit;
