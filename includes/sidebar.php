<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$perfis = $_SESSION['perfis'] ?? [];
$usuario_id = $_SESSION['usuario_id'] ?? 0;

require_once 'funcoes.php';
require_once 'includes/config.php';
$pdo = getPDO();

// Buscar grupos e menus conforme permissões
$sql = "
SELECT mg.id AS grupo_id, mg.nome AS grupo_nome, m.id AS menu_id, m.nome AS menu_nome, m.link, m.icone
FROM menus_grupo mg
JOIN menus m ON m.grupo_id = mg.id AND m.ativo = 1
LEFT JOIN menus_acesso ma ON ma.menu_id = m.id
WHERE mg.ativo = 1
  AND (
    ma.perfil_id IN (" . implode(',', $perfis ?: [0]) . ")
    OR ma.usuario_id = :usuario_id
    OR (ma.perfil_id IS NULL AND ma.usuario_id IS NULL)
  )
ORDER BY mg.ordem, m.ordem
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario_id' => $usuario_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por grupo
$menuAgrupado = [];
foreach ($rows as $row) {
    $menuAgrupado[$row['grupo_nome']][] = $row;
}
?>

<aside class="left-sidebar bg-sidebar">
  <div id="sidebar" class="sidebar sidebar-with-footer">

    <!-- Marca da aplicação -->
    <div class="app-brand">
      <a href="index.php" title="COREME">
        <img 
          src="assets/img/logo-coremehub.png" 
          alt="Logo COREME" 
          class="brand-logo" 
          style="max-height:40px; width:auto;"
        />
      </a>
    </div>

    <div class="" data-simplebar style="height: 100%;">
      <ul class="nav sidebar-inner" id="sidebar-menu">

        <!-- DASHBOARD -->
        <li class="has-sub active expand">
          <a class="sidenav-item-link" href="index.php">
            <i class="mdi mdi-view-dashboard-outline"></i>
            <span class="nav-text">Dashboard</span>
          </a>
        </li>

        <?php foreach ($menuAgrupado as $grupo => $menus): ?>
          <li class="has-sub">
            <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse" data-target="#<?= md5($grupo) ?>" aria-expanded="false" aria-controls="<?= md5($grupo) ?>">
              <i class="mdi mdi-menu"></i>
              <span class="nav-text"><?= htmlspecialchars($grupo) ?></span> <b class="caret"></b>
            </a>
            <ul class="collapse" id="<?= md5($grupo) ?>" data-parent="#sidebar-menu">
              <div class="sub-menu">
                <?php foreach ($menus as $menu): ?>
                  <li>
                    <a class="sidenav-item-link" href="<?= htmlspecialchars($menu['link']) ?>">
                      <span class="nav-text"><?= htmlspecialchars($menu['menu_nome']) ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </div>
            </ul>
          </li>
        <?php endforeach; ?>

      </ul>
    </div>
  </div>
</aside>
