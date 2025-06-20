<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../includes/config.php';

$pdo = getPDO();

$usuario_id = $_POST['usuario_id'] ?? null;

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$perfis = $_POST['perfis'] ?? [];
$ativo = isset($_POST['ativo']) ? (int)$_POST['ativo'] : 1;

$telefone = $_POST['telefone'] ?? null;
$data_nascimento = $_POST['data_nascimento'] ?? null;
$cpf = $_POST['cpf'] ?? null;
$sexo = $_POST['sexo'] ?? null;
$cep = $_POST['cep'] ?? null;
$cidade = $_POST['cidade'] ?? null;
$estado = $_POST['estado'] ?? null;
$endereco = $_POST['endereco'] ?? null;
$host = $_SERVER['HTTP_HOST'];
$subdominio = explode('.', $host)[0]; // 'hilda' em 'hilda.coremehub.com.br'


// Imagem de perfil
$imagem_nome = null;
if (!empty($_FILES['imagem_perfil']['name'])) {
    $ext = pathinfo($_FILES['imagem_perfil']['name'], PATHINFO_EXTENSION);
    $nome_arquivo = 'user_' . time() . '.' . $ext;

    // Pasta correta por subdomínio
    $subpasta = "../assets/img/user/$subdominio/";
    if (!is_dir($subpasta)) {
        mkdir($subpasta, 0777, true); // Cria pasta do hospital
    }

    // Caminho relativo a ser salvo no banco
    $imagem_nome = "$subdominio/$nome_arquivo";
    $destino = $subpasta . $nome_arquivo;

    move_uploaded_file($_FILES['imagem_perfil']['tmp_name'], $destino);
}


if (!$nome || !$email) {
    header("Location: ../users.php?erro=Campos obrigatórios faltando");
    exit;
}

if ($usuario_id) {
    // Atualiza dados do usuário
    $sql = "UPDATE usuarios SET nome = ?, email = ?, ativo = ?";
    $params = [$nome, $email, $ativo];

    if ($senha) {
        $sql .= ", senha = ?";
        $params[] = password_hash($senha, PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = ?";
    $params[] = $usuario_id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Atualiza dados adicionais
    $sqlDados = "UPDATE usuarios_dados SET telefone = ?, data_nascimento = ?, cpf = ?, sexo = ?, cep = ?, cidade = ?, estado = ?, endereco = ?";
    if ($imagem_nome) {
        $sqlDados .= ", imagem_perfil = ?";
    }
    $sqlDados .= " WHERE usuario_id = ?";

    $paramsDados = [$telefone, $data_nascimento, $cpf, $sexo, $cep, $cidade, $estado, $endereco];
    if ($imagem_nome) {
        $paramsDados[] = $imagem_nome;
    }
    $paramsDados[] = $usuario_id;

    $stmt = $pdo->prepare($sqlDados);
    $stmt->execute($paramsDados);

} else {
    // NOVO USUÁRIO
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: ../users.php?erro=E-mail já cadastrado");
        exit;
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, ativo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nome, $email, $senha_hash, $ativo]);
    $usuario_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("
        INSERT INTO usuarios_dados (
            usuario_id, telefone, data_nascimento, cpf, sexo,
            cep, cidade, estado, endereco, imagem_perfil
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $usuario_id, $telefone, $data_nascimento, $cpf, $sexo,
        $cep, $cidade, $estado, $endereco, $imagem_nome ?: 'user.png'
    ]);
}

// PERFIS – sempre atualizar independente de novo ou edição
$pdo->prepare("DELETE FROM usuario_perfis WHERE usuario_id = ?")->execute([$usuario_id]);

$stmtPerfil = $pdo->prepare("SELECT id FROM perfis WHERE nome = ? LIMIT 1");

foreach ($perfis as $perfil_nome) {
    $stmtPerfil->execute([$perfil_nome]);
    $perfil_id = $stmtPerfil->fetchColumn();

    if ($perfil_id) {
        $stmtVinculo = $pdo->prepare("INSERT INTO usuario_perfis (usuario_id, perfil_id) VALUES (?, ?)");
        $stmtVinculo->execute([$usuario_id, $perfil_id]);
    }
}

header("Location: ../users.php?ok=1");
exit;
