<?php
session_start();
require_once __DIR__ . '/includes/config.php';

$nome     = trim($_POST['nome'] ?? '');
$email    = trim($_POST['email'] ?? '');
$senha    = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

// Validação básica
if (!$nome || !$email || !$senha || !$confirmar) {
    header("Location: sign-up.php?erro=Preencha todos os campos.");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: sign-up.php?erro=E-mail inválido.");
    exit;
}

if ($senha !== $confirmar) {
    header("Location: sign-up.php?erro=Senhas não coincidem.");
    exit;
}

$pdo = getPDO();

// Verifica se o e-mail já está cadastrado
$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    header("Location: sign-up.php?erro=E-mail já está em uso.");
    exit;
}

// Cria o hash da senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Insere o novo usuário
$stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
$stmt->execute([$nome, $email, $senha_hash]);

$usuario_id = $pdo->lastInsertId();

// Atribui perfil de residente
$stmtPerfil = $pdo->prepare("SELECT id FROM perfis WHERE nome = 'residente'");
$stmtPerfil->execute();
$perfil_id = $stmtPerfil->fetchColumn();

if ($perfil_id) {
    $stmtVinculo = $pdo->prepare("INSERT INTO usuario_perfis (usuario_id, perfil_id) VALUES (?, ?)");
    $stmtVinculo->execute([$usuario_id, $perfil_id]);
}

// Redireciona com sucesso
header("Location: sign-up.php?ok=1");
exit;
