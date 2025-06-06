<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../sign-in.php");
    exit;
}

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

$id = $_GET['id'] ?? null;
$avaliacao_id = $_GET['avaliacao_id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM avaliacoes_criterios WHERE id = ?");
    $stmt->execute([$id]);
}

// Redireciona de volta para a edição da avaliação
header("Location: ../cadastro_avaliacoes.php?id=" . urlencode($avaliacao_id));
exit;
