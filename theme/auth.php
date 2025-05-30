<?php
session_start();
require_once __DIR__ . '/includes/config.php'; // Conexão PDO

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';

// 1. Verifica se os campos foram preenchidos
if (empty($email) || empty($senha)) {
    header("Location: sign-in.php?erro=1");
    exit;
}

// 2. Conecta ao banco
$pdo = getPDO();

// 3. Busca o usuário pelo e-mail
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND ativo = 1");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    // 4. Autenticação bem-sucedida
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];

    // 5. Carrega os perfis do usuário
    $stmtPerfis = $pdo->prepare("
        SELECT p.nome 
        FROM usuario_perfis up 
        JOIN perfis p ON up.perfil_id = p.id 
        WHERE up.usuario_id = ?
    ");
    $stmtPerfis->execute([$usuario['id']]);
    $_SESSION['perfis'] = $stmtPerfis->fetchAll(PDO::FETCH_COLUMN);

    // 6. Redireciona para o painel
    header("Location: dashboard.php");
    exit;

} else {
    // Falha no login
    header("Location: sign-in.php?erro=1");
    exit;
}
