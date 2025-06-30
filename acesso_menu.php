<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: sign-in.php");
    exit;
}

require_once 'includes/config.php';
require_once 'includes/funcoes.php';

verificarAcessoRecurso('acesso_menu');

$pdo = getPDO();

// Carregar perfis e usuários
$perfis = $pdo->query("SELECT id, nome FROM perfis ORDER BY nome")->fetchAll();
$usuarios = $pdo->query("SELECT id, nome FROM usuarios ORDER BY nome")->fetchAll();

$menus = $pdo->query("
  SELECT m.id, m.nome, mg.nome AS grupo_nome
  FROM menus m
  LEFT JOIN menus_grupo mg ON m.grupo_id = mg.id
  WHERE m.ativo = 1
  ORDER BY mg.ordem, m.ordem, m.nome
")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);


// Identificar se perfil ou usuário foi selecionado
$alvo_tipo = $_GET['tipo'] ?? 'perfil';
$alvo_id = $_GET['id'] ?? null;

$acessosMarcados = [];
if ($alvo_id) {
    $stmt = $pdo->prepare("SELECT menu_id FROM menus_acesso WHERE {$alvo_tipo}_id = ?");
    $stmt->execute([$alvo_id]);
    $acessosMarcados = array_column($stmt->fetchAll(), 'menu_id');
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<?php include 'includes/topbar.php'; ?>

<div class="container mt-4">
    <h4>Controle de Acesso aos Menus</h4>

    <form method="GET" class="form-inline mb-4">
        <label class="mr-2">Escolher por:</label>
        <select name="tipo" class="form-control mr-2" onchange="this.form.submit()">
            <option value="perfil" <?= $alvo_tipo === 'perfil' ? 'selected' : '' ?>>Perfil</option>
            <option value="usuario" <?= $alvo_tipo === 'usuario' ? 'selected' : '' ?>>Usuário</option>
        </select>

        <select name="id" class="form-control mr-2" onchange="this.form.submit()">
            <option value="">Selecione...</option>
            <?php
            $lista = $alvo_tipo === 'perfil' ? $perfis : $usuarios;
            foreach ($lista as $item):
                ?>
                <option value="<?= $item['id'] ?>" <?= $alvo_id == $item['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($item['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($alvo_id): ?>
        <form method="POST" action="actions/salvar_acesso_menu.php">
            <input type="hidden" name="tipo" value="<?= $alvo_tipo ?>">
            <input type="hidden" name="id" value="<?= $alvo_id ?>">

            <div class="form-group">
                <label>Menus Permitidos:</label>
                <?php foreach ($menus as $grupoNome => $itens): ?>
                    <h5 class="mt-4 mb-2"><?= htmlspecialchars($grupoNome ?: 'Sem Grupo') ?></h5>
                    <div class="row">
                        <?php foreach ($itens as $menu): ?>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="menus[]" value="<?= $menu['id'] ?>"
                                        <?= in_array($menu['id'], $acessosMarcados) ? 'checked' : '' ?>>
                                    <label class="form-check-label"><?= htmlspecialchars($menu['nome']) ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

            </div>

            <button type="submit" class="btn btn-primary">Salvar Acessos</button>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>