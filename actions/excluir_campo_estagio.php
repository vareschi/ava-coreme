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

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../campos_estagio.php?erro=id_nao_informado");
    exit;
}

// Exclusão lógica: marca como inativo e define data_exclusao
try {
    $stmt = $pdo->prepare("UPDATE campos_estagio SET status = 0, data_exclusao = NOW() WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: ../campos_estagio.php?sucesso=1");
    exit;
} catch (PDOException $e) {
    header("Location: ../campos_estagio.php?erro=bd");
    exit;
}
