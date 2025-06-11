<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

// Coleta dados do formulário
$avaliacao_gerada_id = $_POST['avaliacao_gerada_id'] ?? null;
$observacoes = trim($_POST['observacoes'] ?? '');
$criterios = $_POST['criterios'] ?? [];

if (!$avaliacao_gerada_id || empty($criterios)) {
    die('Avaliação ou critérios não informados.');
}

// Apaga notas anteriores (para reavaliação)
$stmt = $pdo->prepare("DELETE FROM avaliacoes_respostas WHERE avaliacao_gerada_id = ?");
$stmt->execute([$avaliacao_gerada_id]);

// Insere as novas respostas
$stmtInsert = $pdo->prepare("
    INSERT INTO avaliacoes_respostas (avaliacao_gerada_id, criterio_id, nota_atribuida, data_avaliacao) 
    VALUES (?, ?, ?, NOW())
");

foreach ($criterios as $criterio_id => $nota) {
    $nota = is_numeric($nota) ? (float) $nota : null;
    if ($nota === null) continue; // pular se nota inválida
    $stmtInsert->execute([$avaliacao_gerada_id, $criterio_id, $nota]);
}

// Verifica o total de critérios ativos da avaliação
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM avaliacoes_criterios ac
    JOIN avaliacoes_perguntas ap ON ap.id = ac.pergunta_id AND ap.status = 1
    JOIN avaliacoes_geradas ag ON ag.modelo_id = ap.avaliacao_id
    WHERE ag.id = ? AND ac.status = 1
");
$stmt->execute([$avaliacao_gerada_id]);
$total_criterios = (int) $stmt->fetchColumn();

// Compara com quantidade de respostas preenchidas
$total_respostas = count($criterios);
$status = ($total_respostas >= $total_criterios && $total_criterios > 0) ? 3 : 2; // 3 = Finalizado, 2 = Iniciado

// Atualiza o status e observações
$stmt = $pdo->prepare("
    UPDATE avaliacoes_geradas 
    SET status = ?, observacoes_preceptor = ?, data_atualizacao = NOW()
    WHERE id = ?
");
$stmt->execute([$status, $observacoes, $avaliacao_gerada_id]);

header("Location: ../avaliar.php?sucesso=1");
exit;
