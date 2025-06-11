<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

$avaliacao_gerada_id = $_POST['avaliacao_gerada_id'] ?? null;
$observacoes = trim($_POST['observacoes'] ?? '');
$criterios = $_POST['criterios'] ?? [];

if (!$avaliacao_gerada_id || empty($criterios)) {
    die('Avaliação ou critérios não informados.');
}

// Apaga notas anteriores (caso exista reavaliação)
$stmt = $pdo->prepare("DELETE FROM avaliacoes_respostas WHERE avaliacao_id = ?");
$stmt->execute([$avaliacao_gerada_id]);

// Insere as novas notas
foreach ($criterios as $criterio_id => $nota) {
    $stmt = $pdo->prepare("INSERT INTO avaliacoes_respostas (avaliacao_id, criterio_id, nota) VALUES (?, ?, ?)");
    $stmt->execute([$avaliacao_gerada_id, $criterio_id, $nota]);
}

// Verifica se todos os critérios foram preenchidos
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM avaliacoes_criterios ac 
                       JOIN avaliacoes_geradas ag ON ag.modelo_id = ac.avaliacao_id
                       JOIN avaliacoes_perguntas ap ON ap.id = ac.pergunta_id
                       WHERE ag.id = ? AND ac.status = 1 AND ap.status = 1");
$stmt->execute([$avaliacao_gerada_id]);
$total_criterios = $stmt->fetchColumn();

$total_respostas = count($criterios);
$status = ($total_respostas >= $total_criterios) ? 3 : 2; // 3 = Finalizado, 2 = Iniciado

// Atualiza a avaliação gerada
$stmt = $pdo->prepare("UPDATE avaliacoes_geradas 
                       SET status = ?, observacoes_preceptor = ?, data_atualizacao = NOW()
                       WHERE id = ?");
$stmt->execute([$status, $observacoes, $avaliacao_gerada_id]);

header("Location: ../avaliar.php?sucesso=1");
exit;
