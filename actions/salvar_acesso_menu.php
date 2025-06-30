<?php
require_once '../includes/config.php';
session_start();

$pdo = getPDO();

$tipo = $_POST['tipo'] ?? 'perfil';
$id = $_POST['id'] ?? null;
$menusSelecionados = $_POST['menus'] ?? [];

if (!$id || !in_array($tipo, ['perfil', 'usuario'])) {
    header("Location: ../acesso_menu.php");
    exit;
}

// Remove os acessos anteriores
$campo = $tipo . '_id';
$stmt = $pdo->prepare("DELETE FROM menus_acesso WHERE {$campo} = ?");
$stmt->execute([$id]);

// Insere os novos acessos
$stmt = $pdo->prepare("INSERT INTO menus_acesso (menu_id, {$campo}) VALUES (?, ?)");
foreach ($menusSelecionados as $menuId) {
    $stmt->execute([$menuId, $id]);
}

header("Location: ../acesso_menu.php?tipo={$tipo}&id={$id}&salvo=1");
exit;
