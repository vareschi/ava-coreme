<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/config.php';

$pdo = getPDO();

// Verifica se está logado
if (!isset($_SESSION['usuario_id'])) {
    die('Acesso negado. Usuário não autenticado.');
}

// Coleta os dados do formulário
$titulo = trim($_POST['titulo'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$especialidade_id = !empty($_POST['especialidade_id']) ? $_POST['especialidade_id'] : null;
$data_criacao = date('Y-m-d H:i:s');

// Validação básica
if (empty($titulo) ) {
    die('Campos obrigatórios não preenchidos.');
}

try {
    $stmt = $pdo->prepare("INSERT INTO avaliacoes (titulo, descricao, especialidade_id, data_criacao) 
                       VALUES (:titulo, :descricao, :especialidade_id, :data_criacao)");

    $stmt->bindValue(':titulo', $titulo);
    $stmt->bindValue(':descricao', $descricao);
    $stmt->bindValue(':especialidade_id', $especialidade_id, $especialidade_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
    $stmt->bindValue(':data_criacao', $data_criacao);

    $stmt->execute();


    header("Location: ../avaliacoes.php?ok=1");
    exit;
} catch (PDOException $e) {
    die("Erro ao salvar avaliação: " . $e->getMessage());
}
