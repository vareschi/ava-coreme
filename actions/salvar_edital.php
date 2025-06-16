<?php
session_start();
require_once '../includes/config.php';

$pdo = getPDO();

$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? '';
$numero = $_POST['numero'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$data_abertura = $_POST['data_abertura'] ?? '';
$data_fechamento = $_POST['data_fechamento'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$especialidades = $_POST['especialidades'] ?? [];

if (!$nome || !$numero || !$data_abertura || !$data_fechamento || !$tipo) {
    header("Location: ../editar_edital.php?erro=campos_obrigatorios");
    exit;
}

if ($id) {
    // Atualiza edital
    $stmt = $pdo->prepare("UPDATE editais SET nome = ?, numero = ?, descricao = ?, data_abertura = ?, data_fechamento = ?, tipo = ? WHERE id = ?");
    $stmt->execute([$nome, $numero, $descricao, $data_abertura, $data_fechamento, $tipo, $id]);

    // Remove especialidades anteriores
    $pdo->prepare("DELETE FROM edital_especialidades WHERE edital_id = ?")->execute([$id]);
} else {
    // Cria novo edital
    $stmt = $pdo->prepare("INSERT INTO editais (nome, numero, descricao, data_abertura, data_fechamento, tipo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $numero, $descricao, $data_abertura, $data_fechamento, $tipo]);
    $id = $pdo->lastInsertId();
}

// Salva especialidades vinculadas
foreach ($especialidades as $esp_id) {
    $stmt = $pdo->prepare("INSERT INTO edital_especialidades (edital_id, especialidade_id) VALUES (?, ?)");
    $stmt->execute([$id, $esp_id]);
}

// Obtém subdomínio
$host = $_SERVER['HTTP_HOST'];
$subdominio = explode('.', $host)[0]; // Ex: 'hilda'

// Salva arquivos
if (!empty($_FILES['anexos']['name'][0])) {
    $arquivos = $_FILES['anexos'];
    $pasta_destino = "../uploads/$subdominio/";

    // Garante que a pasta existe
    if (!is_dir($pasta_destino)) {
        mkdir($pasta_destino, 0777, true);
    }

    for ($i = 0; $i < count($arquivos['name']); $i++) {
        $nome_original = basename($arquivos['name'][$i]);
        $nome_arquivo = time() . '_' . $nome_original;
        $caminho_relativo = "$subdominio/" . $nome_arquivo; // Caminho salvo no banco
        $caminho_completo = $pasta_destino . $nome_arquivo;  // Caminho absoluto no servidor

        if (move_uploaded_file($arquivos['tmp_name'][$i], $caminho_completo)) {
            $stmt = $pdo->prepare("INSERT INTO edital_arquivos (edital_id, nome_arquivo, caminho_arquivo) VALUES (?, ?, ?)");
            $stmt->execute([$id, $nome_original, $caminho_completo]);
        }
    }
}


header("Location: ../editais.php?ok=1");
exit;
?>