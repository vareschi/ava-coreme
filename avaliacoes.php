<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

include 'includes/sidebar.php';
include 'includes/topbar.php';

$pdo = getPDO();

// Filtro de busca
$filtro = $_GET['filtro'] ?? '';
$sql = "SELECT a.*, e.nome AS especialidade_nome 
        FROM avaliacoes a 
        LEFT JOIN especialidades e ON a.especialidade_id = e.id 
        WHERE a.status != 'Inativa'";

$params = [];
if ($filtro) {
    $sql .= " AND a.titulo LIKE :filtro";
    $params[':filtro'] = "%$filtro%";
}
$sql .= " ORDER BY a.data_criacao DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
  <h2>Avaliações</h2>

  <form class="row g-3 mb-3" method="GET">
    <div class="col-md-10">
      <input type="text" name="filtro" class="form-control" placeholder="Buscar por título..." value="<?= htmlspecialchars($filtro) ?>">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Buscar</button>
    </div>
  </form>

  <a href="cadastro_avaliacao.php" class="btn btn-success mb-3">Nova Avaliação</a>

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Título</th>
          <th>Descrição</th>
          <th>Especialidade</th>
          <th>Período</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($avaliacoes) === 0): ?>
          <tr><td colspan="6">Nenhuma avaliação encontrada.</td></tr>
        <?php else: ?>
          <?php foreach ($avaliacoes as $av): ?>
            <tr>
              <td><?= htmlspecialchars($av['titulo']) ?></td>
              <td><?= htmlspecialchars($av['descricao']) ?></td>
              <td><?= htmlspecialchars($av['especialidade_nome'] ?? '-') ?></td>
              <td><?= htmlspecialchars($av['periodo']) ?></td>
              <td><?= htmlspecialchars($av['status']) ?></td>
              <td>
                <a href="editar_avaliacao.php?id=<?= $av['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="actions/excluir_avaliacao.php?id=<?= $av['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir esta avaliação?')">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
