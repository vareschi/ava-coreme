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

// Salva arquivos
if (!empty($_FILES['anexos']['name'][0])) {
    $arquivos = $_FILES['anexos'];
    for ($i = 0; $i < count($arquivos['name']); $i++) {
        $nome_arquivo = basename($arquivos['name'][$i]);
        $caminho_arquivo = '../uploads/' . time() . '_' . $nome_arquivo;

        if (move_uploaded_file($arquivos['tmp_name'][$i], '../' . $caminho_arquivo)) {
            $stmt = $pdo->prepare("INSERT INTO edital_arquivos (edital_id, nome_arquivo, caminho_arquivo) VALUES (?, ?, ?)");
            $stmt->execute([$id, $nome_arquivo, $caminho_arquivo]);
        }
    }
}

header("Location: ../editais.php?ok=1");
exit;
?>