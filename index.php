<?php
session_start();

// Se o usuário NÃO estiver logado, redireciona para o sign-in do subdomínio atual
if (!isset($_SESSION['usuario_id'])) {
    $host = $_SERVER['HTTP_HOST']; // Captura o domínio ou subdomínio atual
    header("Location: https://{$host}/sign-in.php");
    exit;
}

// Se estiver logado, redireciona para a página de usuários
header("Location: users.php");
exit;
