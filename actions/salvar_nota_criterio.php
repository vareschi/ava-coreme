<?php
session_start();

$usuario_id = $_SESSION['usuario_id'] ?? 0;
if ($usuario_id <= 0) {
    die('Usuário não autenticado.');
}


ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();

// Validação dos dados recebidos
$avaliacao_gerada_id = $_POST['avaliacao_gerada_id'] ?? null;
$observacoes = trim($_POST['observacoes'] ?? '');
$criterios = $_POST['criterios'] ?? [];

if (!$avaliacao_gerada_id || !is_numeric($avaliacao_gerada_id)) {
    die('ID da avaliação inválido ou ausente.');
}

if (empty($criterios) || !is_array($criterios)) {
    die('Nenhuma nota foi informada.');
}

try {
    $pdo->beginTransaction();

    // Verifica se a avaliação existe
    $stmt = $pdo->prepare("SELECT * FROM avaliacoes_geradas WHERE id = ?");
    $stmt->execute([$avaliacao_gerada_id]);
    $avaliacao = $stmt->fetch();

    if (!$avaliacao) {
        throw new Exception('Avaliação gerada não encontrada.');
    }

    // Remove respostas anteriores
    $stmt = $pdo->prepare("DELETE FROM avaliacoes_respostas WHERE avaliacao_gerada_id = ?");
    $stmt->execute([$avaliacao_gerada_id]);

    echo 'Pergunta ID: ' . $pergunta_id;

    // Insere as novas respostas
    $stmt = $pdo->prepare("INSERT INTO avaliacoes_respostas (avaliacao_gerada_id, criterio_id, nota_atribuida,usuario_id) VALUES (?,?, ?, ?)");

    foreach ($criterios as $criterio_id => $nota) {
        if (!is_numeric($criterio_id) || !is_numeric($nota)) {
            continue; // Ignora valores inválidos
        }
        $stmt->execute([$avaliacao_gerada_id, $criterio_id, $nota, $usuario_id]);
    }

    // Verifica total de critérios esperados
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM avaliacoes_criterios ac
                           JOIN avaliacoes_perguntas ap ON ap.id = ac.pergunta_id AND ap.status = 1
                           WHERE ap.avaliacao_id = ? AND ac.status = 1");
    $stmt->execute([$avaliacao['modelo_id']]);
    $total_criterios = $stmt->fetchColumn();
    $total_respostas = count($criterios);

    $status = ($total_respostas >= $total_criterios) ? 3 : 2; // Finalizado ou Iniciado

    // Atualiza a avaliação
    $stmt = $pdo->prepare("UPDATE avaliacoes_geradas
                           SET status = ?, observacoes_preceptor = ?, data_atualizacao = NOW()
                           WHERE id = ?");
    $stmt->execute([$status, $observacoes ?: null, $avaliacao_gerada_id]);

    $pdo->commit();

    header("Location: ../avaliar.php?sucesso=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erro ao salvar avaliação: " . $e->getMessage());
}
