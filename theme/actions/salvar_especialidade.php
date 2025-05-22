<?php
require_once '../includes/config.php';

$id = intval($_POST['id'] ?? 0);
$nome = trim($_POST['nome'] ?? '');
$duracao = intval($_POST['duracao'] ?? 0);

if ($nome && $duracao > 0) {
    $pdo = getPDO();
    
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE especialidades SET nome = ?, duracao_anos = ? WHERE id = ?");
        $stmt->execute([$nome, $duracao, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO especialidades (nome, duracao_anos) VALUES (?, ?)");
        $stmt->execute([$nome, $duracao]);
    }
}

header('Location: ../especialidades.php');
exit;
