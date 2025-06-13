<?php

// Carrega lista de hospitais e dados
$hospitais = require __DIR__ . '/config_hospitais.php';

// Detecta o host atual
$host = $_SERVER['HTTP_HOST'] ?? '';

// Verifica se o host é reconhecido
if (!isset($hospitais[$host])) {
    die("Hospedagem não configurada para o domínio: {$host}");
}

// Seleciona config do hospital atual
$config = $hospitais[$host];

// Cria conexão com PDO
function getPDO() {
    global $config;

    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['user'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}
