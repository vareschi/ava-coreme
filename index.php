<?php
session_start();

// Se o usuário não estiver logado, redireciona para o sign-in do subdomínio atual
if (!isset($_SESSION['usuario_id'])) {
    $host = $_SERVER['HTTP_HOST']; // Captura o domínio ou subdomínio atual
    header("Location: https://{$host}/sign-in.php");
    exit;
}


