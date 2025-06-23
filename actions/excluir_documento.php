<?php
require_once '../includes/config.php';
$pdo = getPDO();

$documento_id = $_POST['documento_id'] ?? null;
$usuario_id = $_POST['usuario_id'] ?? null;

if ($documento_id) {
    $stmt = $pdo->prepare("UPDATE documentos SET data_exclusao = NOW() WHERE id = ?");
    $stmt->execute([$documento_id]);
}

header("Location: ../residente.php?usuario_id=$usuario_id");
exit;
