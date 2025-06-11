<?php
session_start();

// Destruir todos os dados da sessão
$_SESSION = [];
session_unset();
session_destroy();

// Redirecionar para a tela de login (ou qualquer outra página)
header("Location: sign-in.php");
exit;
