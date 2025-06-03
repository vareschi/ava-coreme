<?php
require_once 'includes/header.php';
require_once 'includes/config.php';

include 'includes/sidebar.php';
include 'includes/topbar.php';

$pdo = getPDO();

$especialidades = $pdo->query("SELECT id, nome FROM especialidades ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

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

  <!-- Botão para abrir a modal -->
  <div class="mb-3">
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalAvaliacao">
      Nova Avaliação
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Título</th>
          <th>Descrição</th>
          <th>Especialidade</th>
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

<!-- Botão para abrir a modal -->
<div class="mb-3">
  <button class="btn btn-primary" data-toggle="modal" data-target="#modalAvaliacao">
    Nova Avaliação
  </button>
</div>

<!-- Modal -->
<div class="modal fade" id="modalAvaliacao" tabindex="-1" role="dialog" aria-labelledby="modalAvaliacaoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="actions/salvar_avaliacao.php" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAvaliacaoLabel">Cadastrar Avaliação</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="titulo">Título</label>
            <input type="text" class="form-control" name="titulo" id="titulo" required>
          </div>
          <div class="form-group">
            <label for="descricao">Descrição</label>
            <textarea class="form-control" name="descricao" id="descricao" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label for="especialidade_id">Especialidade</label>
            <select class="form-control" name="especialidade_id" id="especialidade_id" required>
              <option value="">Selecione</option>
              <?php foreach ($especialidades as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</div>


<?php require_once 'includes/footer.php'; ?>
