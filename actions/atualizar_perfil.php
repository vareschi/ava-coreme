<?php
session_start();
require_once '../includes/config.php';

$pdo = getPDO();
$usuario_id = $_POST['usuario_id'] ?? null;
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha_atual = $_POST['senha_atual'] ?? '';
$nova_senha = $_POST['nova_senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

if (!$usuario_id || !$nome || !$email) {
    header("Location: ../user-profile.php?erro=Campos obrigatórios");
    exit;
}

// Valida a senha antiga se a nova for informada
if ($nova_senha) {
    $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $senha_hash = $stmt->fetchColumn();

    if (!password_verify($senha_atual, $senha_hash)) {
        header("Location: ../user-profile.php?erro=Senha atual incorreta");
        exit;
    }

    if ($nova_senha !== $confirmar_senha) {
        header("Location: ../user-profile.php?erro=Senhas não coincidem");
        exit;
    }

    $senha_final = password_hash($nova_senha, PASSWORD_DEFAULT);
} else {
    $senha_final = null; // Não atualizar senha
}

// Atualiza a imagem, se houver
$imagem_perfil = null;
if (!empty($_FILES['imagem_perfil']['name'])) {
    $ext = pathinfo($_FILES['imagem_perfil']['name'], PATHINFO_EXTENSION);
    $imagem_nome = 'user_' . time() . '.' . $ext;
    
    $host = $_SERVER['HTTP_HOST'];
    $subdominio = explode('.', $host)[0];
    $pasta = "../assets/img/user/$subdominio/";
    
    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $destino = $pasta . $imagem_nome;
    move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $destino);
    $imagem_perfil = "$subdominio/" . $imagem_nome;
}

// Atualiza tabela usuarios
$sql = "UPDATE usuarios SET nome = ?, email = ?";
$params = [$nome, $email];

if ($senha_final) {
    $sql .= ", senha = ?";
    $params[] = $senha_final;
}
$sql .= " WHERE id = ?";
$params[] = $usuario_id;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Atualiza tabela usuarios_dados
$sql_dados = "UPDATE usuarios_dados SET data_nascimento = data_nascimento"; // dummy
$params_dados = [];

if ($imagem_perfil) {
    $sql_dados .= ", imagem_perfil = ?";
    $params_dados[] = $imagem_perfil;
}

$sql_dados .= " WHERE usuario_id = ?";
$params_dados[] = $usuario_id;

$stmt = $pdo->prepare($sql_dados);
$stmt->execute($params_dados);

header("Location: ../perfil.php?ok=1");
exit;
