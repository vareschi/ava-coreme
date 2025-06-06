<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();




if (!isset($_SESSION['usuario_id'])) {
    header("Location: sign-in.php");
    exit;
}

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

$avaliacao_id = $_POST['avaliacao_id'] ?? null;
$pergunta = $_POST['pergunta'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$nota_maxima = $_POST['nota_maxima'] ?? '';

// Validação básica
if (!$avaliacao_id || empty($pergunta) || empty($tipo) || $nota_maxima === '') {
    // Você pode personalizar esse retorno com uma mensagem flash ou log de erro
    header("Location: cadastro_avaliacoes.php?id=" . urlencode($avaliacao_id));
    exit;
}

// Inserção no banco
$stmt = $pdo->prepare("INSERT INTO avaliacoes_perguntas (avaliacao_id, pergunta, tipo, nota_maxima) VALUES (?, ?, ?, ?)");
$stmt->execute([$avaliacao_id, $pergunta, $tipo, $nota_maxima]);

header("Location: cadastro_avaliacoes.php?id=" . urlencode($avaliacao_id));
exit;
