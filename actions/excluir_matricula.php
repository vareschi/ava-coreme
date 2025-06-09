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

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../matriculas.php?erro=id_nao_informado");
    exit;
}

// Atualiza data_exclusao como exclusão lógica
$stmt = $pdo->prepare("UPDATE matriculas SET data_exclusao = NOW(), status=0 WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../matriculas.php?sucesso=excluida");
exit;
