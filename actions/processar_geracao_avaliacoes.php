<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/funcoes.php';

$pdo = getPDO();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../sign-in.php");
    exit;
}

try {
    $tipo = $_POST['tipo_geracao'];
    $modelo_id = $_POST['modelo_avaliacao_id'];
    $campo_estagio_id = $_POST['campo_estagio_id'];
    $inicio = $_POST['inicio_avaliacao'];
    $fim = $_POST['fim_avaliacao'];
    $nivel = $_POST['nivel_especialidade'];
    $ano = $_POST['ano_letivo'];
    $mes = $_POST['mes_referencia'];
    $preceptoresSelecionados = $_POST['preceptores'] ?? []; // agora é um array

    $residentes = [];

    if ($tipo === 'turma' && !empty($_POST['turma_id'])) {
        $stmt = $pdo->prepare("SELECT usuario_id FROM matriculas WHERE turma_id = ? AND status = 1");
        $stmt->execute([$_POST['turma_id']]);
        $residentes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } elseif ($tipo === 'residente' && !empty($_POST['residente_id'])) {
        $residentes = [$_POST['residente_id']];
    }

    if (empty($residentes)) {
        throw new Exception("Nenhum residente encontrado.");
    }

    $stmtInsert = $pdo->prepare("INSERT INTO avaliacoes_geradas (
        modelo_id, residente_id, preceptor_id, campo_estagio_id,
        inicio_avaliacao, fim_avaliacao, nivel_especialidade, ano_letivo, mes_referencia
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    foreach ($residentes as $residente_id) {
        $preceptores = [];

        if (!empty($preceptoresSelecionados)) {
            // Usar os preceptores escolhidos manualmente
            $preceptores = array_map('intval', $preceptoresSelecionados);
        } else {
            // Busca todos os preceptores vinculados ao campo
            $stmtPrec = $pdo->prepare("
                SELECT p.usuario_id AS preceptor_id 
                FROM preceptor_campoestagio pc
                JOIN preceptores p ON pc.preceptor_id = p.id
                WHERE pc.campo_estagio_id = ?
            ");
            $stmtPrec->execute([$campo_estagio_id]);
            $preceptores = $stmtPrec->fetchAll(PDO::FETCH_COLUMN);
        }


        foreach ($preceptores as $preceptor_id) {
            $stmtInsert->execute([
                $modelo_id,
                $residente_id,
                $preceptor_id,
                $campo_estagio_id,
                $inicio,
                $fim,
                $nivel,
                $ano,
                $mes
            ]);
        }
    }

    header("Location: ../avaliar.php?sucesso=1");
    exit;
} catch (Exception $e) {
    echo "Erro ao gerar avaliações: " . $e->getMessage();
}
