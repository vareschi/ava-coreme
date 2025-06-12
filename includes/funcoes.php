<?php

//Função Para buscar 1 Perfil
function temPerfil($id) {
    return in_array($id, $_SESSION['perfis'] ?? []);
}

//Função Para buscar entre Vários Perfis
function temAlgumPerfil(array $ids) {
    $meusPerfis = $_SESSION['perfis'] ?? [];
    return (bool) array_intersect($meusPerfis, $ids);
}

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
        echo "
        <div style='
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #fdfdfd;
            font-family: Arial, sans-serif;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            text-align: center;
        '>
            <h2 style='color: #c0392b;'>Acesso Negado</h2>
            <p style='color: #555; font-size: 16px;'>
                Você não tem permissão para acessar esta área.<br>
                Caso acredite que isso seja um erro, entre em contato com o administrador.
            </p>
            <a href='index.php' style='
                display: inline-block;
                margin-top: 20px;
                padding: 10px 20px;
                background-color: #3498db;
                color: #fff;
                text-decoration: none;
                border-radius: 5px;
            '>Voltar</a>
        </div>
        ";
        exit;
    }

}

