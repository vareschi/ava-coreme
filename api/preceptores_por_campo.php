<?php
require_once '../includes/config.php';

$campo_id = $_GET['campo_id'] ?? null;

if (!$campo_id) {
    echo json_encode([]);
    exit;
}

$pdo = getPDO();

$sql = "SELECT u.id, u.nome 
        FROM preceptores p
        JOIN usuarios u ON u.id = p.usuario_id
        JOIN preceptor_campoestagio pc ON pc.preceptor_id = p.id
        WHERE pc.campo_estagio_id = ?
        ORDER BY u.nome";

$stmt = $pdo->prepare($sql);
$stmt->execute([$campo_id]);

$preceptores = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($preceptores);
