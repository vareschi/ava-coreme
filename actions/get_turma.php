<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Usuário não autenticado']);
    exit;
}

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('turmas');

$pdo = getPDO();

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['erro' => 'ID não informado']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM turmas WHERE id = ?");
$stmt->execute([$id]);
$turma = $stmt->fetch(PDO::FETCH_ASSOC);

if ($turma) {
    // Formata datas para o formato do input date
    $turma['data_abertura'] = date('Y-m-d', strtotime($turma['data_abertura']));
    $turma['data_fechamento'] = date('Y-m-d', strtotime($turma['data_fechamento']));

    // Garante que preceptor_id estará presente (mesmo que null)
    if (!isset($turma['preceptor_id'])) {
        $turma['preceptor_id'] = null;
    }

    echo json_encode($turma);
} else {
    http_response_code(404);
    echo json_encode(['erro' => 'Turma não encontrada']);
}
