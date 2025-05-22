<?php
require_once 'includes/config.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("DELETE FROM especialidades WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: especialidades.php');
exit;
