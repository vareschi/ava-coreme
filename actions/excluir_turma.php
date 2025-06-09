<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../sign-in.php");
    exit;
}

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('turmas');

$pdo = getPDO();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../turmas.php?erro=ID não informado");
    exit;
}

// Atualiza o campo de exclusão lógica
$stmt = $pdo->prepare("UPDATE turmas SET data_exclusao = NOW(), status = 0 WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../turmas.php?sucesso=Turma excluída");
exit;
