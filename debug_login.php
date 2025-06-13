<?php
session_start();
require_once 'includes/config.php'; // ajuste o caminho se necessário

$pdo = getPDO();

$email = 'residenciamedica.hildasribeiro@gmail.com';
$senha = '123456';

echo "<h3>Debug de login para: <code>$email</code></h3><hr>";

// Etapa 1: Buscar usuário por e-mail
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario) {
    echo "<p style='color:red;'>Usuário não encontrado no banco de dados.</p>";
    exit;
}

echo "<p style='color:green;'>Usuário encontrado: <strong>{$usuario['nome']}</strong></p>";
echo "<p>Status: {$usuario['status']}</p>";

// Etapa 2: Verificar se está ativo
if ($usuario['status'] != 1) {
    echo "<p style='color:orange;'>Usuário encontrado, mas está <strong>inativo</strong> (status = {$usuario['status']}).</p>";
    exit;
}

// Etapa 3: Verificar senha
if (password_verify($senha, $usuario['senha'])) {
    echo "<p style='color:green;'>Senha correta ✅</p>";
} else {
    echo "<p style='color:red;'>Senha incorreta ❌</p>";
    echo "<p>Hash salvo no banco: <code>{$usuario['senha']}</code></p>";
    $hashTeste = password_hash($senha, PASSWORD_DEFAULT);
    echo "<p>Hash gerado com password_hash('123456'): <code>$hashTeste</code></p>";
}
