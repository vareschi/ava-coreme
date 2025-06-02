<?php
session_start();
require_once '../includes/config.php';

$pdo = getPDO();

$id = $_GET['id'] ?? null;
$edital_id = $_GET['edital_id'] ?? null;

if ($id && $edital_id) {
    $stmt = $pdo->prepare("SELECT caminho_arquivo FROM edital_arquivos WHERE id = ?");
    $stmt->execute([$id]);
    $arquivo = $stmt->fetchColumn();

    if ($arquivo && file_exists('../' . $arquivo)) {
        unlink('../' . $arquivo);
    }

    $pdo->prepare("DELETE FROM edital_arquivos WHERE id = ?")->execute([$id]);
}

header("Location: ../editar_edital.php?id=$edital_id");
exit;
