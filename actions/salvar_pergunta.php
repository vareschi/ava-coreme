<?php
require_once 'includes/config.php';

$avaliacao_id = $_POST['avaliacao_id'];
$pergunta = $_POST['pergunta'];
$tipo = $_POST['tipo'];
$nota_maxima = $_POST['nota_maxima'];

$stmt = $pdo->prepare("INSERT INTO avaliacoes_perguntas (avaliacao_id, pergunta, tipo, nota_maxima) VALUES (?, ?, ?, ?)");
$stmt->execute([$avaliacao_id, $pergunta, $tipo, $nota_maxima]);

header("Location: cadastro_avaliacoes.php?id=$avaliacao_id");
exit;
