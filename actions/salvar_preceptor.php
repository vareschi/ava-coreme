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

verificarAcessoRecurso('usuarios');

$pdo = getPDO();

$usuario_id      = $_POST['usuario_id'] ?? null;
$data_inicio     = $_POST['data_inicio'] ?? null;
especialidade_id = $_POST['especialidade_id'] ?? null;
$coordenador     = isset($_POST['coordenador']) ? 1 : 0;
$campos_estagio  = $_POST['campos_estagio'] ?? [];

if (!$usuario_id || !$data_inicio || !$especialidade_id) {
    header("Location: ../preceptores.php?usuario_id=$usuario_id&erro=campos_obrigatorios");
    exit;
}

// Verifica se já existe cadastro de preceptor
$stmt = $pdo->prepare("SELECT id FROM preceptores WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$preceptor = $stmt->fetch();

if ($preceptor) {
    // Atualiza
    $stmt = $pdo->prepare("UPDATE preceptores SET data_inicio = ?, especialidade_id = ?, coordenador = ?, data_alteracao = NOW() WHERE usuario_id = ?");
    $stmt->execute([$data_inicio, $especialidade_id, $coordenador, $usuario_id]);
    $preceptor_id = $preceptor['id'];
} else {
    // Insere
    $stmt = $pdo->prepare("INSERT INTO preceptores (usuario_id, data_inicio, especialidade_id, coordenador) VALUES (?, ?, ?, ?)");
    $stmt->execute([$usuario_id, $data_inicio, $especialidade_id, $coordenador]);
    $preceptor_id = $pdo->lastInsertId();
}

// Remove todos os vínculos atuais
$stmt = $pdo->prepare("DELETE FROM preceptor_campoestagio WHERE preceptor_id = ?");
$stmt->execute([$preceptor_id]);

// Insere novos vínculos
if (!empty($campos_estagio)) {
    $stmt = $pdo->prepare("INSERT INTO preceptor_campoestagio (preceptor_id, campo_estagio_id) VALUES (?, ?)");
    foreach ($campos_estagio as $campo_id) {
        $stmt->execute([$preceptor_id, $campo_id]);
    }
}

header("Location: ../preceptores.php?usuario_id=$usuario_id&sucesso=1");
exit;
