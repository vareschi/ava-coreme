<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../sign-in.php");
    exit;
}

require_once '../includes/config.php';
require_once '../includes/funcoes.php';

verificarAcessoRecurso('usuarios');

$pdo = getPDO();

$usuario_id       = $_POST['usuario_id'] ?? null;
$data_inicio      = $_POST['data_inicio'] ?? null;
$especialidade_id = $_POST['especialidade_id'] ?? null;
$coordenador      = $_POST['coordenador'] ?? null;
$campos_estagio   = $_POST['campos_estagio'] ?? [];

if (!$usuario_id) {
    die('Usuário não informado.');
}

try {
    $pdo->beginTransaction();

    // Verifica se já existe
    $stmt = $pdo->prepare("SELECT id FROM preceptores WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $preceptor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($preceptor) {
        // Atualiza
        $stmt = $pdo->prepare("UPDATE preceptores SET data_inicio = ?, especialidade_id = ?, coordenador = ? WHERE usuario_id = ?");
        $stmt->execute([$data_inicio, $especialidade_id, $coordenador, $usuario_id]);

        // Limpa os campos de estágio antigos
        $stmt = $pdo->prepare("DELETE FROM preceptor_campoestagio WHERE preceptor_id = ?");
        $stmt->execute([$preceptor['id']]);
        $preceptor_id = $preceptor['id'];
    } else {
        // Insere novo preceptor
        $stmt = $pdo->prepare("INSERT INTO preceptores (usuario_id, data_inicio, especialidade_id, coordenador) VALUES (?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $data_inicio, $especialidade_id, $coordenador]);
        $preceptor_id = $pdo->lastInsertId();
    }

    // Insere os novos campos de estágio
    if (!empty($campos_estagio)) {
        $stmt = $pdo->prepare("INSERT INTO preceptor_campoestagio (preceptor_id, campo_estagio_id) VALUES (?, ?)");
        foreach ($campos_estagio as $campo_id) {
            $stmt->execute([$preceptor_id, $campo_id]);
        }
    }

    $pdo->commit();

    header("Location: ../preceptor.php?usuario_id=$usuario_id&sucesso=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erro ao salvar preceptor: " . $e->getMessage());
}
