<?php
require_once '../includes/config.php';

$nome = trim($_POST['nome'] ?? '');
$duracao = intval($_POST['duracao'] ?? 0);

if ($nome && $duracao > 0) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("INSERT INTO especialidades (nome, duracao_anos) VALUES (?, ?)");
    $stmt->execute([$nome, $duracao]);
}

header('Location: ../especialidades.php');
exit;
