<?php
function verificarAcessoRecurso(string $recurso) {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }

    $pdo = getPDO();
    $usuarioId = $_SESSION['usuario_id'];

    $stmt = $pdo->prepare("
        SELECT 1
        FROM usuario_perfis up
        JOIN acesso_perfis ap ON ap.perfil_id = up.perfil_id
        WHERE up.usuario_id = ? AND ap.recurso = ?
        LIMIT 1
    ");
    $stmt->execute([$usuarioId, $recurso]);
    $temAcesso = $stmt->fetchColumn();

    if (!$temAcesso) {
        echo "<div style='margin: 20px; font-family: sans-serif; color: red;'>Acesso negado. Você não tem permissão para acessar esta área.</div>";
        exit;
    }
}

