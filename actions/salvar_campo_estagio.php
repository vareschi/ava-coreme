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

verificarAcessoRecurso('campos_estagio');

$pdo = getPDO();

$nome = trim($_POST['nome'] ?? '');

if (empty($nome)) {
    header("Location: ../campos_estagio.php?erro=nome_obrigatorio");
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO campos_estagio (nome) VALUES (?)");
    $stmt->execute([$nome]);

    header("Location: ../campos_estagio.php?sucesso=1");
    exit;
} catch (PDOException $e) {
    header("Location: ../campos_estagio.php?erro=bd");
    exit;
}
