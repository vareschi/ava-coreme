<?php
require_once '../includes/config.php';
$pdo = getPDO();

$documento_id = $_POST['documento_id'] ?? null;
$usuario_id = $_POST['usuario_id'] ?? null;
$origem = $_POST['origem'] ?? 'residente'; // valor padrão

if ($documento_id) {
    $stmt = $pdo->prepare("UPDATE documentos SET data_exclusao = NOW() WHERE id = ?");
    $stmt->execute([$documento_id]);
}


// Após salvar no banco
if ($origem === 'perfil') {
    header("Location: ../perfil.php");
} else {
    header("Location: ../residentes.php?usuario_id=$usuario_id");
}
exit;

