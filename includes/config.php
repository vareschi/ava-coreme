<?php
// includes/config.php

// 1) Defina as constantes de conexão
define('DB_HOST',     'localhost');
define('DB_NAME',     'u835980051_ava');
define('DB_USER',     'u835980051_ava');
define('DB_PASSWORD', '|65wbm^+voU');
define('DB_CHARSET',  'utf8mb4');

// 2) Função que retorna um objeto PDO conectado
function getPDO()
{
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Em produção você poderia logar o erro e mostrar mensagem genérica
        die("Erro na conexão com o banco: " . $e->getMessage());
    }
}
