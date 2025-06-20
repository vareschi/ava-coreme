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

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("UPDATE avaliacoes SET status = 0 WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: ../avaliacoes_modelo.php");
exit;
