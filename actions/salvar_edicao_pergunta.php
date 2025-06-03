<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: sign-in.php");
    exit;
}

require_once 'includes/config.php';
require_once 'includes/funcoes.php';

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

// Coleta os dados do formulário
$id = $_POST['id'] ?? null;
$avaliacao_id = $_POST['avaliacao_id'] ?? null;
$pergunta = $_POST['pergunta'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$nota_maxima = $_POST['nota_maxima'] ?? 0;

// Validação básica
if (!$id || !$avaliacao_id || empty($pergunta) || empty($tipo) || $nota_maxima === '') {
    // Redireciona com erro (pode adicionar tratamento personalizado se quiser)
    header("Location: cadastro_avaliacoes.php?id=" . urlencode($avaliacao_id));
    exit;
}

// Atualiza a pergunta
$stmt = $pdo->prepare("UPDATE avaliacoes_perguntas SET pergunta = ?, tipo = ?, nota_maxima = ? WHERE id = ?");
$stmt->execute([$pergunta, $tipo, $nota_maxima, $id]);

// Redireciona de volta à avaliação
header("Location: cadastro_avaliacoes.php?id=" . urlencode($avaliacao_id));
exit;
