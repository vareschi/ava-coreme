<?php
require_once '../includes/config.php';
$pdo = getPDO();

// Obtém dados enviados
$usuario_id = $_POST['usuario_id'] ?? null;
$id_tipo = $_POST['id_tipo_documento'] ?? null;
$arquivo = $_FILES['arquivo'] ?? null;
$origem = $_POST['origem'] ?? 'residente'; // valor padrão


if (!$usuario_id || !$id_tipo || !$arquivo) {
    die("Dados incompletos");
}

// Detecta o subdomínio/hospital a partir do host
$host = $_SERVER['HTTP_HOST']; // Ex: hospitalx.coremehub.com.br
$partes = explode('.', $host);
$hospital = $partes[0]; // hospitalx
$hospital = preg_replace('/[^a-z0-9_]/i', '_', strtolower($hospital)); // sanitiza

// Caminho final: uploads/documentos/hospital/usuario_id/
$pastaDestino = "../uploads/documentos/{$hospital}/{$usuario_id}/";

// Cria a estrutura de pastas se não existir
if (!is_dir($pastaDestino)) {
    mkdir($pastaDestino, 0777, true);
}

// Gera nome único pro arquivo
$nomeArquivo = time() . '_' . basename($arquivo['name']);
$caminhoCompleto = $pastaDestino . $nomeArquivo;

// Move o arquivo
if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
    // Salva no banco
    $caminhoBanco = "uploads/documentos/{$hospital}/{$usuario_id}/{$nomeArquivo}";
    $stmt = $pdo->prepare("INSERT INTO documentos (residente_id, tipo_documento_id, caminho_arquivo, data_inclusao) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$usuario_id, $id_tipo, $caminhoBanco]);
}


// Após salvar no banco
if ($origem === 'perfil') {
    header("Location: ../perfil.php");
} else {
    header("Location: ../residente.php?usuario_id=$usuario_id");
}
exit;
