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

// Recuperar dados da avaliação gerada para popular os campos corretamente
$stmt = $pdo->prepare("SELECT * FROM avaliacoes_geradas WHERE id = ?");
$stmt->execute([$avaliacao_gerada_id]);
$avaliacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$avaliacao) {
    die('Avaliação gerada não encontrada.');
}

// Apaga respostas anteriores (se houver)
$stmt = $pdo->prepare("DELETE FROM avaliacoes_respostas WHERE avaliacao_gerada_id = ?");
$stmt->execute([$avaliacao_gerada_id]);

// Insere novas respostas com dados completos
foreach ($criterios as $criterio_id => $nota) {
    $stmt = $pdo->prepare("
        INSERT INTO avaliacoes_respostas (
            avaliacao_gerada_id, criterio_id, usuario_id, turma_id, preceptor_id, nota_atribuida, data_avaliacao
        ) VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $avaliacao_gerada_id,
        $criterio_id,
        $avaliacao['residente_id'],
        $avaliacao['turma_id'],
        $avaliacao['preceptor_id'],
        $nota
    ]);
}

// Verifica se todos os critérios foram avaliados
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM avaliacoes_criterios ac
    JOIN avaliacoes_perguntas ap ON ac.pergunta_id = ap.id
    WHERE ap.avaliacao_id = ? AND ac.status = 1 AND ap.status = 1
");
$stmt->execute([$avaliacao['modelo_id']]);
$total_criterios = $stmt->fetchColumn();

$total_respostas = count($criterios);
$status = ($total_respostas >= $total_criterios) ? 3 : 2; // 3 = Finalizado, 2 = Iniciado

// Atualiza status e observações da avaliação
$stmt = $pdo->prepare("UPDATE avaliacoes_geradas 
                       SET status = ?, observacoes_preceptor = ?, data_atualizacao = NOW()
                       WHERE id = ?");
$stmt->execute([$status, $observacoes, $avaliacao_gerada_id]);

header("Location: ../avaliar.php?sucesso=1");
exit;
