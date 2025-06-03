<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: sign-in.php");
    exit;
}

require_once 'includes/config.php';
require_once 'includes/funcoes.php';

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/topbar.php';

verificarAcessoRecurso('avaliacoes');

$pdo = getPDO();
$avaliacoes = $pdo->query("SELECT a.*, e.nome AS especialidade FROM avaliacoes a LEFT JOIN especialidades e ON a.especialidade_id = e.id WHERE a.ativo = 1 ORDER BY a.id DESC")->fetchAll();

?>

<script src="assets/plugins/data-tables/jquery.datatables.min.js"></script>
<script src="assets/plugins/data-tables/datatables.bootstrap4.min.js"></script>

<div class="content">
  <div class="card">
    <div class="card-body">
      <h4 class="card-title d-flex justify-content-between align-items-center">
        Avaliações
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAvaliacao">
          Nova Avaliação
        </button>
      </h4>
      <hr>
      <table id="hoverable-data-table" class="table table-hover nowrap" style="width:100%">
        <thead>
          <tr>
            <th>Título</th>
            <th>Descrição</th>
            <th>Especialidade</th>
            <th class="text-right">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($avaliacoes as $av): ?>
            <tr>
              <td><?= htmlspecialchars($av['titulo']) ?></td>
              <td><?= htmlspecialchars($av['descricao']) ?></td>
              <td><?= htmlspecialchars($av['especialidade']) ?></td>
              <td class="text-right">
                <a href="cadastro_avaliacao.php?id=<?= $av['id'] ?>" class="text-warning me-2">
                  <i class="mdi mdi-pencil"></i>
                </a>
                <a href="actions/excluir_avaliacao.php?id=<?= $av['id'] ?>" class="text-danger" onclick="return confirm('Tem certeza que deseja excluir esta avaliação?')">
                  <i class="mdi mdi-delete"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Nova Avaliação -->
<div class="modal fade" id="modalAvaliacao" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
  <div class="modal-dialog">
    <form action="actions/salvar_avaliacao.php" method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tituloModal">Nova Avaliação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Título</label>
          <input type="text" name="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Descrição</label>
          <textarea name="descricao" class="form-control"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Especialidade</label>
          <select name="especialidade_id" class="form-select" required>
            <option value="">Selecione</option>
            <?php
              $especialidades = $pdo->query("SELECT id, nome FROM especialidades ORDER BY nome")->fetchAll();
              foreach ($especialidades as $e): ?>
              <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Salvar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'includes/footer.php'; ?>
