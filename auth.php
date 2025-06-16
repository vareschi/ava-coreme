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

// 3. Busca o usuário pelo e-mail (sem filtrar ativo ainda)
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

// 4. Verifica se usuário existe e a senha confere
if ($usuario && password_verify($senha, $usuario['senha'])) {
    // 5. Verifica se está ativo
    if ($usuario['ativo'] != 1) {
        // Usuário existe, senha ok, mas está inativo
        header("Location: sign-in.php?erro=2"); // erro=2 = aguardando ativação
        exit;
    }

    // 6. Autenticação bem-sucedida
    session_regenerate_id(true); 
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];

    // 7. Carrega os perfis
    $stmtPerfis = $pdo->prepare("SELECT perfil_id FROM usuario_perfis WHERE usuario_id = ?");
    $stmtPerfis->execute([$usuario['id']]);
    $_SESSION['perfis'] = $stmtPerfis->fetchAll(PDO::FETCH_COLUMN);

    // 8. Redirecionamento por perfil
    if ($_SESSION['perfis'] === ['4'] || $_SESSION['perfis'] === [4]) {
        header("Location: avaliar.php");
    } else {
        header("Location: users.php");
    }
    exit;

} else {
    // Falha no login
    header("Location: sign-in.php?erro=1");
    exit;
}
