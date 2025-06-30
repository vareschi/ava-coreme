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

verificarAcessoRecurso('campos_estagio');

$pdo = getPDO();

// Dados do formulário
$id   = $_POST['id'] ?? null;
$nome = trim($_POST['nome'] ?? '');
$cor  = $_POST['cor'] ?? '#2196f3';

// Validação básica
if (empty($nome)) {
    header("Location: ../campos_estagio.php?erro=nome_obrigatorio");
    exit;
}

try {
    if ($id) {
        // Atualização
        $stmt = $pdo->prepare("UPDATE campos_estagio SET nome = ?, cor = ? WHERE id = ?");
        $stmt->execute([$nome, $cor, $id]);
    } else {
        // Inserção
        $stmt = $pdo->prepare("INSERT INTO campos_estagio (nome, cor, status) VALUES (?, ?, 1)");
        $stmt->execute([$nome, $cor]);
    }

    header("Location: ../campos_estagio.php?sucesso=1");
    exit;
} catch (PDOException $e) {
    header("Location: ../campos_estagio.php?erro=bd");
    exit;
}
