<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../sign-in.php");
    exit;
}

require_once '../includes/config.php';

$pdo = getPDO();

$id = $_POST['id'] ?? null;
$descricao = trim($_POST['descricao'] ?? '');
$avaliacao_id = $_POST['avaliacao_id'] ?? null;

if ($id && $descricao) {
    $stmt = $pdo->prepare("UPDATE avaliacoes_criterios SET descricao = ? WHERE id = ?");
    $stmt->execute([$descricao, $id]);
}

header("Location: ../cadastro_avaliacoes.php?id=" . urlencode($avaliacao_id));
exit;
